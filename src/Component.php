<?php

declare(strict_types=1);

namespace Verplanke\CraftComponents;

use craft\helpers\App;
use Verplanke\CraftComponents\Enums\Driver;
use Verplanke\CraftComponents\Exceptions\ComponentException;
use Throwable;
use function Sentry\captureException;

/**
 * @internal
 */
abstract class Component
{
    /**
     * @return object|array<string, mixed>
     */
    abstract protected static function redis(): object|array;

    /**
     * @return object|array<string, mixed>
     */
    abstract protected static function database(): object|array;

    public static string $moduleId = 'CraftComponents';

    /**
     * @return object|array<string, mixed>
     * @throws \Verplanke\CraftComponents\Exceptions\ComponentException
     */
    public static function driver(Driver|string $driver): object|array
    {
        $component = class_basename(static::class);

        try {
            $value = is_string($driver)
                ? $driver
                : $driver->value;

            return match ($value) {
                Driver::REDIS->value => static::redis(),
                Driver::DATABASE->value => static::database(),
                default => throw new ComponentException("Unsupported $component driver [$value]."),
            };
        } catch (Throwable $throwable) {
            if (function_exists('Sentry\captureException')) {
                captureException($throwable);
            }

            throw new ComponentException($throwable->getMessage(), $throwable->getCode(), $throwable);
        }
    }

    /**
     * @throws \yii\base\Exception
     */
    protected static function prefixKey(string $component): string
    {
        $hash = hash(
            'xxh128',
            sprintf('%s_%s', App::env('CRAFT_APP_ID'), App::env('CRAFT_ENVIRONMENT'))
        );

        return sprintf(
            '%s_%s_%s_',
            strtolower(class_basename($component)),
            self::$moduleId,
            $hash,
        );
    }
}
