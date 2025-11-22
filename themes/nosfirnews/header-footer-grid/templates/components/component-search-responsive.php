<?php
use NosfirNews\HeaderFooterGrid\Core\Components\SearchResponsive;
( new SearchResponsive( isset( $args ) ? $args : [] ) )->render();