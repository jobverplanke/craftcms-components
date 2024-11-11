<?php

declare(strict_types=1);

namespace Verplanke\CraftComponents\Components;

use craft\helpers\App;
use Verplanke\CraftComponents\Component;
use Verplanke\CraftComponents\Exceptions\QueueException;
use yii\queue\redis\Queue as YiiRedisQueue;

class Queue extends Component
{
    /**
     * @return array<string, mixed>
     * @throws \Verplanke\CraftComponents\Exceptions\RedisException
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    protected static function redis(): array
    {
        $redis = Redis::connect();
        $redis['database'] = App::env('REDIS_QUEUE_DB') ?: 3;

        $config = [
            'class' => YiiRedisQueue::class,
            'redis' => $redis,
            'channel' => App::env('REDIS_QUEUE_CHANNEL') ?: 'queue',
            'ttr' => App::env('REDIS_QUEUE_TTR') ?: 300,
            'attempts' => App::env('REDIS_QUEUE_ATTEMPTS') ?: 3,
        ];

        return ! defined('Craft::Personal')
            ? ['proxyQueue' => $config]
            : $config;
    }

    /**
     * Craft uses a custom queue driver based on the Yii Queue database driver as default queue driver.
     *
     * @see https://github.com/yiisoft/yii2-queue/blob/master/docs/guide/driver-db.md
     * @see https://craftcms.com/docs/4.x/config/app.html#queue
     * @see https://craftcms.com/docs/5.x/reference/config/app.html#queue
     *
     * @return array<string, mixed>
     * @throws \Verplanke\CraftComponents\Exceptions\QueueException
     */
    protected static function database(): array
    {
        throw new QueueException('Remove the queue component configuration from the app.php to make use of the database queue driver as it is Craft default queue driver.');
    }
}
