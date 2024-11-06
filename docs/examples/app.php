<?php

declare(strict_types=1);

use craft\helpers\App;
use Verplanke\CraftComponents\Components\Queue;
use Verplanke\CraftComponents\Components\Redis;
use Verplanke\CraftComponents\Components\Cache;

return [
    'id' => App::env('CRAFT_APP_ID') ?: 'CraftCMS',
    'components' => [
        'redis' => Redis::connect(),
        'cache' => fn () => Cache::driver('redis'),
        'queue' => Queue::driver('redis'),
    ],
];
