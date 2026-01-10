<?php
namespace NosfirNews\HeaderFooterGrid\Core\Settings;
class Config {
    protected static $data = [];
    public static function set( $key, $value ) { self::$data[ $key ] = $value; }
    public static function get( $key, $default = null ) { return array_key_exists( $key, self::$data ) ? self::$data[ $key ] : $default; }
    public static function all() { return self::$data; }
}