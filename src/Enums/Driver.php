<?php

declare(strict_types=1);

namespace Verplanke\CraftComponents\Enums;

enum Driver: string
{
    case REDIS = 'redis';
    case DATABASE = 'database';
}
