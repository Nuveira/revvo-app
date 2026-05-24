<?php
// hitung base url biar asset path bener di semua kedalaman folder
$rootDir = str_replace('\\', '/', dirname(__DIR__));
$docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
define('BASE_URL', str_replace($docRoot, '', $rootDir));
define('ROOT_PATH', dirname(__DIR__));

function asset($path) {
    return BASE_URL . '/' . ltrim($path, '/');
}

function url($path) {
    return BASE_URL . '/' . ltrim($path, '/');
}
