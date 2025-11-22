<?php
namespace NosfirNews\HeaderFooterGrid\Core;
class Magic_Tags {
    public static function parse( $text ) {
        $map = [ '{site_title}' => get_bloginfo( 'name' ), '{site_description}' => get_bloginfo( 'description' ), '{home_url}' => home_url( '/' ) ];
        return strtr( (string) $text, $map );
    }
}