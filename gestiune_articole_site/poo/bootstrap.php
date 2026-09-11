<?php
declare(strict_types=1);
require_once __DIR__ . '/PortalModels.php';
spl_autoload_register(function (string $class): void {
    $prefix = 'PortalOOP\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $path = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($path)) {
        require_once $path;
        return;
    }
    if (!class_exists($class, false) && !interface_exists($class, false) && !trait_exists($class, false)) {
        require_once __DIR__ . '/PortalModels.php';
    }
});
