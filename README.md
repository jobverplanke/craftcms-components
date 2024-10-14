## Introduction
Simple driver setup for CraftCMS components queue, cache or session for CraftCMS v4 and v5.

Available components and drivers

|              |       Redis        |       Database       |
|:-------------|:------------------:|:--------------------:|
| **Cache**    | :white_check_mark: |  :white_check_mark:  |
| **Queue**    | :white_check_mark: | :x: (default driver) |
| **Session**  | :white_check_mark: |  :white_check_mark:  |

> [!NOTE]
> Craft's default queue driver is the database.
> Do not configure the queue component in the app.php if you want to make use of the database queue driver.
> 
> Craft v4: https://craftcms.com/docs/4.x/config/app.html#queue <br>
> Craft v5: https://craftcms.com/docs/5.x/reference/config/app.html#queue

## Configuration
To make use of a different driver per component is a breeze. Just specify the desired driver for the component you want to use in the `components` config array in either `config/app.php` or `config/app.web.php` (session only).

If you want to use the Redis driver for a specific component, make sure Redis is configured.

### Redis configuration
1. Add the Redis component to the `app.php`, see example below
2. Enable Redis by adding the following environment variables
   - `REDIS_ENABLED=true`
   - `REDIS_URL=` OR `REDIS_HOST`, `REDIS_PORT` and `REDIS_PASSWORD`

`REDIS_URL` takes precedence over `REDIS_HOST`, `REDIS_PORT` and `REDIS_PASSWORD`.
```php
// config/app.php

use Verplanke\CraftComponents\Components\Redis;

return [
    'components' => [
        'redis' => Redis::connect(),
    ],
];
```

### Cache component

```php
// config/app.php

use Verplanke\CraftComponents\Components\Cache;

return [
    'components' => [
        'cache' => fn () => Cache::driver('redis'),
    ],
];
```

### Queue component

```php
// config/app.php

use Verplanke\CraftComponents\Components\Queue;

return [
    'components' => [
        'queue' => Queue::driver('redis'),
    ],
];
```

### Session component

```php
// config/app.web.php

use Verplanke\CraftComponents\Components\Session;

return [
    'components' => [
        'session' => fn () => Session::driver('redis'),
    ],
];
```

### Environment Variable Configuration
The following environment variables are available for further configuration of the components<br />
It's recommended to leave the following set to `false` when using Heroku. [Heroku issue](https://help.heroku.com/HC0F8CUS/redis-connection-issues) and [Github issue](https://github.com/phpredis/phpredis/issues/1941) 
- `REDIS_SSL`
- `REDIS_SSL_VERIFY_PEER`
- `REDIS_SSL_VERIFY_PEER_NAME`


|                              |      Required       |  Type   | Default Value | Description / Remark                                                                                                        |
|:-----------------------------|:-------------------:|:-------:|:-------------:|-----------------------------------------------------------------------------------------------------------------------------|
| `REDIS_ENABLED`              | :white_check_mark:  | boolean |               |                                                                                                                             |
| `REDIS_URL`                  | :white_check_mark:  | string  |               | Takes precedence over `REDIS_HOST`, `REDIS_PORT` and `REDIS_PASSWORD`.                                                      |
| `REDIS_HOST`                 |                     | string  |               | **Only when `REDIS_URL` is not used**                                                                                       |
| `REDIS_PORT`                 |                     | string  |               | **Only when `REDIS_URL` is not used**                                                                                       |
| `REDIS_PASSWORD`             |                     | string  |               | **Only when `REDIS_URL` is not used**                                                                                       |
| `REDIS_DB`                   |                     | integer |       0       | Default database to use, this database will be used for all Redis connections                                               |  
| `REDIS_RETRIES`              |                     | integer |       1       | Th..ount of times to retry connecting after connection has timed out                                                        |
| `REDIS_SSL`                  |                     | boolean |     false     | Use SSL connection when connecting with Redis. Recommended to use `false` when using Heroku                                 |
| `REDIS_SSL_VERIFY_PEER`      |                     | boolean |     false     | Verify peer SSL certificate. Recommended to use `false` when using Heroku                                                   |
| `REDIS_SSL_VERIFY_PEER_NAME` |                     | boolean |     false     | Verify peer name of SSL certificate. Recommended to use `false` when using Heroku                                           |
| `REDIS_CACHE_DB`             |                     | integer |       1       | Which database to use for the cache database. Make sure the cache database is separated from the queue and session database |
| `REDIS_QUEUE_DB`             |                     | integer |       3       | Which database to use for the queue database                                                                                |
| `REDIS_QUEUE_CHANNEL`        |                     | string  |     queue     | Queue channel to use                                                                                                        |
| `REDIS_QUEUE_TTR`            |                     | integer |      300      | Max time for job execution, unit in seconds                                                                                 |
| `REDIS_QUEUE_ATTEMPTS`       |                     | integer |       3       | Max number of attempts                                                                                                      |
| `REDIS_SESSION_DB`           |                     | integer |       1       | Which database to use for the session database.                                                                             |

