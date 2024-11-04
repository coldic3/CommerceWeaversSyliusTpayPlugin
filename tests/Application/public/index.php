<?php

declare(strict_types=1);

use App\Kernel;

$_SERVER['APP_RUNTIME_OPTIONS'] = [
    'project_dir' => dirname(__DIR__),
];

require_once dirname(__DIR__, 3).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
