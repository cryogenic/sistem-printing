<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

register_shutdown_function(function () {
    $e = error_get_last();
    if ($e) {
        echo PHP_EOL . "FATAL: " . $e['message'] . PHP_EOL . "FAIL: " . $e['file'] . " baris " . $e['line'] . PHP_EOL;
    }
});

try {
    echo "1. autoload..." . PHP_EOL;
    require __DIR__ . '/vendor/autoload.php';
    echo "2. app..." . PHP_EOL;
    $app = require __DIR__ . '/bootstrap/app.php';
    echo "3. kernel bootstrap..." . PHP_EOL;
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "4. OK, Laravel boleh hidup" . PHP_EOL;
} catch (Throwable $t) {
    echo "ERROR: " . get_class($t) . PHP_EOL . $t->getMessage() . PHP_EOL;
    echo $t->getFile() . " baris " . $t->getLine() . PHP_EOL;
}