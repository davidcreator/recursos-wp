<?php
namespace NosfirNews\HeaderFooterGrid;
class Main {
    private static $instance;
    public static function instance() {
        if (!self::$instance) self::$instance = new self();
        return self::$instance;
    }
    public function init() {
        add_action('after_setup_theme', [$this, 'setup']);
    }
    public function setup() {
        add_theme_support('widgets');
    }
}