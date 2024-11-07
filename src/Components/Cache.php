<?php

declare(strict_types=1);

namespace Verplanke\CraftComponents\Components;

use Craft;
use craft\cache\DbCache;
use craft\helpers\App;
use Verplanke\CraftComponents\Component;
use yii\redis\Cache as YiiRedisCache;

class Cache extends Component
{
    /**
     * @throws \Verplanke\CraftComponents\Exceptions\RedisException
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    protected static function redis(): YiiRedisCache
    {
        $redis = Redis::connect();
        $redis['database'] = App::env('REDIS_CACHE_DB') ?: 1;

        return Craft::createObject([
            'class' => YiiRedisCache::class,
            'keyPrefix' => static::prefixKey(self::class),
            'defaultDuration' => Craft::$app?->getConfig()->getGeneral()->cacheDuration ?? 86400,
            'redis' => $redis,
        ]);
    }

    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    protected static function database(): DbCache
    {
        return Craft::createObject([
            'class' => DbCache::class,
            'keyPrefix' => static::prefixKey(self::class),
            'defaultDuration' => Craft::$app?->getConfig()->getGeneral()->cacheDuration ?? 86400,
        ]);
    }
}
