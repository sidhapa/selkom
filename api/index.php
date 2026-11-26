<?php

$dirs = [
    '/tmp/writable',
    '/tmp/writable/cache',
    '/tmp/writable/logs',
    '/tmp/writable/session',
    '/tmp/writable/uploads',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

require __DIR__ . '/../public/index.php';