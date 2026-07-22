<?php
/**
 * Application Core Autoloader & Bootstrapper
 */

declare(strict_types=1);

// PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/../';

    if (strpos($class, 'Core\\') === 0) {
        $file = $baseDir . 'core/' . str_replace('\\', '/', substr($class, 5)) . '.php';
    } elseif (strpos($class, 'App\\') === 0) {
        $file = $baseDir . 'app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    } else {
        return;
    }

    if (file_exists($file)) {
        require_once $file;
    }
});

// Start session if not CLI
if (php_sapi_name() !== 'cli') {
    \Core\Session::start();
}

// Error & Exception Logging Configuration
$config = require __DIR__ . '/../config/app.php';
$isDebug = $config['debug'] ?? true;

error_reporting(E_ALL);
ini_set('log_errors', '1');

$logDir = dirname(__DIR__) . '/logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}
ini_set('error_log', $logDir . '/error.log');

if ($isDebug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}

