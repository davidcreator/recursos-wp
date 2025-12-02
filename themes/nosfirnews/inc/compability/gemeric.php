<?php
function nosfirnews_comp_generic_active( $class_or_function ) {
    return class_exists( $class_or_function ) || function_exists( $class_or_function );
}
