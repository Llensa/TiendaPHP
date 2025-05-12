<?php
if (!defined('BASE_URL')) {
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $path = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', '/' . explode('/', $_SERVER['REQUEST_URI'])[1]); // o mejor: $path
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
