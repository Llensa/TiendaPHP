<?php
class Flash {
    public static function set($key, $msg) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['flash'][$key] = $msg;
    }

    public static function get($key) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!empty($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
        return null;
    }

    public static function has($key) {
        return isset($_SESSION['flash'][$key]);
    }

    public static function show($key, $class = 'mensaje exito') {
        if (self::has($key)) {
            echo '<p class="' . $class . '">' . htmlspecialchars(self::get($key)) . '</p>';
        }
    }
}
