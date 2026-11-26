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

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

if (!empty($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = preg_replace('#^/index\.php(?=/|$)#', '', $_SERVER['REQUEST_URI']) ?: '/';
}

if (!empty($_SERVER['PATH_INFO'])) {
    $_SERVER['PATH_INFO'] = preg_replace('#^/index\.php(?=/|$)#', '', $_SERVER['PATH_INFO']) ?: '/';
}

require __DIR__ . '/../public/index.php';