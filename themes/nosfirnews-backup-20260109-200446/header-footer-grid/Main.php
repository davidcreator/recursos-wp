<?php
namespace NosfirNews\HeaderFooterGrid;
class Main {
    private static $instance; public static function instance(){ if(!self::$instance) self::$instance=new self(); return self::$instance; }
    public function init(){ add_action('wp_head',[ $this,'head' ]); }
    public function head(){ echo ''; }
}
