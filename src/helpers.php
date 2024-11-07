<?php

declare(strict_types=1);

namespace Verplanke\CraftComponents;

function class_basename(object|string $class): string
{
    $class = is_object($class) ? get_class($class) : $class;

    return basename(str_replace('\\', '/', $class));
}
