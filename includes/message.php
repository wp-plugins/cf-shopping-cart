<?php

namespace cfshoppingcart;

class message {

    private static $messages;
    private static $error_count;

    public function __construct() {
        self::$messages = array();
        self::$error_count = 0;
    }

    public static function add($msg, $error = false) {
        self::$messages[] = esc_html($msg);
        if ($error) {
            self::$error_count++;
        }
    }

    public static function get_error_count() {
        return self::$error_count;
    }

    public static function clear() {
        self::$messages = array();
        self::$error_count = 0;
    }

    public static function get_text() {
        if (!self::$messages) {
            return '';
        }
        return join("\n", self::$messages);
    }

    public static function get() {
        return self::$messages;
    }

}
