<?php

declare(strict_types=1);

use Verplanke\CraftComponents\Components\Session;

return [
    'components' => [
        'session' => fn () => Session::driver(App::env('CRAFT_SESSION_DRIVER')),
    ],
];
