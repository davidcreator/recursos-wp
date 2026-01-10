<?php
namespace NosfirNews\HeaderFooterGrid\Core;
class Magic_Tags {
    public static function parse( $text ) {
        $map = [ '{{site_name}}' => get_bloginfo( 'name' ), '{{home_url}}' => home_url( '/' ) ];
        return strtr( $text, $map );
    }
}
