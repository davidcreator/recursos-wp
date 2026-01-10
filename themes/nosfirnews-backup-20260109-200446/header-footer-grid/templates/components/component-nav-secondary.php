<?php
use NosfirNews\HeaderFooterGrid\Core\Components\SecondNav;
( new SecondNav( isset( $args ) ? $args : [] ) )->render();