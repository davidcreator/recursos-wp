<?php
use NosfirNews\HeaderFooterGrid\Core\Components\Search;
( new Search( isset( $args ) ? $args : [] ) )->render();