add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'nosfirnews-theme',
        get_template_directory_uri() . '/assets/js/theme.js',
        [],
        filemtime(get_template_directory() . '/assets/js/theme.js'),
        true
    );
});