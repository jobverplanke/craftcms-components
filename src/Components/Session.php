<?php

declare(strict_types=1);

namespace Verplanke\CraftComponents\Components;

use Craft;
use craft\behaviors\SessionBehavior;
use craft\helpers\App;
use Verplanke\CraftComponents\Component;
use yii\redis\Session as YiiRedisSession;
use yii\web\DbSession;

class Session extends Component
{
    /**
     * @throws \Verplanke\CraftComponents\Exceptions\RedisException
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public static function redis(): YiiRedisSession
    {
        $redis = Redis::connect();
        $redis['database'] = App::env('REDIS_SESSION_DB') ?: 2;

        $config = array_merge(self::config(), [
            'class' => YiiRedisSession::class,
            'keyPrefix' => static::prefixKey(self::class),
            'redis' => $redis,
        ]);

        return Craft::createObject($config);
    }

    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    protected static function database(): DbSession
    {
        $config = array_merge(self::config(), [
            'class' => DbSession::class,
        ]);

        return Craft::createObject($config);
    }

    /**
     * @return array<string, mixed>
     * @throws \yii\base\Exception
     */
    private static function config(): array
    {
        $stateKeyPrefix = hash('xxh128', static::prefixKey(self::class));

        return [
            'as session' => SessionBehavior::class,
            'flashParam' => $stateKeyPrefix . '__flash',
            'authAccessParam' => $stateKeyPrefix . '__auth_access',
            'name' => Craft::$app?->getConfig()->getGeneral()->phpSessionName ?? 'CraftSessionId',
            'cookieParams' => Craft::cookieConfig(),
        ];
    }
}
