<?php
use NosfirNews\HeaderFooterGrid\Core\Components\Nav;
( new Nav( isset( $args ) ? $args : [] ) )->render();