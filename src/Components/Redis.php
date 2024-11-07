<?php

declare(strict_types=1);

namespace Verplanke\CraftComponents\Components;

use craft\helpers\App;
use Exception;
use Verplanke\CraftComponents\Exceptions\RedisException;
use Throwable;
use yii\redis\Connection;
use function Sentry\captureException;

class Redis
{
    /**
     * @return array<string, mixed>
     * @throws \Verplanke\CraftComponents\Exceptions\RedisException
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public static function connect(): array
    {
        try {
            if (! Redis::isEnabled()) {
                throw new Exception('Redis is not enabled. Enable Redis using environment variable REDIS_ENABLED');
            }

            Redis::ensureRedisExtensionIsEnabled();
            Redis::ensureYiiRedisExtensionIsInstalled();

            return Redis::parseConfiguration(Redis::parseUrl());
        } catch (Throwable $throwable) {
            if (function_exists('Sentry\captureException')) {
                captureException($throwable);
            }

            throw $throwable;
        }
    }

    /**
     * @throws \yii\base\Exception
     */
    public static function isEnabled(): bool
    {
        return match (App::env('REDIS_ENABLED')) {
            true, 'true', 0 => true,
            default => false,
        };
    }

    /**
     * @param array<string> $urlComponents
     * @return array<string, mixed>
     * @throws \yii\base\Exception
     */
    private static function parseConfiguration(array $urlComponents): array
    {
        return [
            'class' => Connection::class,
            'scheme' => $urlComponents['scheme'],
            'hostname' => $urlComponents['hostname'],
            'port' => $urlComponents['port'],
            'password' => $urlComponents['password'],
            'useSSL' => App::env('REDIS_SSL') ?: false,
            'retries' => App::env('REDIS_RETRIES') ?: 1,
            'database' => App::env('REDIS_DB') ?: 0,
            'contextOptions' => [
                'ssl' => [
                    'verify_peer' => App::env('REDIS_SSL_VERIFY_PEER') ?: false,
                    'verify_peer_name' => App::env('REDIS_SSL_VERIFY_PEER_NAME') ?: false,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     * @throws \yii\base\Exception
     */
    private static function parseUrl(): array
    {
        $parsed = parse_url(App::env('REDIS_URL') ?: '');

        $scheme = match ($parsed['scheme'] ?? null) {
            'rediss' => 'tls',
            default => 'tcp',
        };

        return [
            'scheme' => $scheme,
            'hostname' => $parsed['host'] ?? App::env('REDIS_HOST') ?: $parsed['path'] ?? null,
            'port' => $parsed['port'] ?? App::env('REDIS_PORT') ?: 6379,
            'user' => $parsed['user'] ?? null,
            'password' => $parsed['pass'] ?? App::env('REDIS_PASSWORD') ?: null,
        ];
    }

    /**
     * @throws \Verplanke\CraftComponents\Exceptions\RedisException
     */
    private static function ensureRedisExtensionIsEnabled(): void
    {
        if (! extension_loaded('redis')) {
            throw new RedisException('PHP Redis extension (ext-redis) not installed.');
        }
    }

    /**
     * @throws \Verplanke\CraftComponents\Exceptions\RedisException
     */
    private static function ensureYiiRedisExtensionIsInstalled(): void
    {
        if (! class_exists(Connection::class)) {
            throw new RedisException('Missing Yii Redis extension, run [composer require yiisoft/yii2-redis] to install.');
        }
    }
}
