<?php
namespace NosfirNews\HeaderFooterGrid;
function load() {
    require_once get_template_directory() . '/header-footer-grid/Main.php';
    require_once get_template_directory() . '/header-footer-grid/functions-template.php';
    require_once get_template_directory() . '/header-footer-grid/functions-migration.php';
    require_once get_template_directory() . '/header-footer-grid/Core/Css_Generator.php';
    require_once get_template_directory() . '/header-footer-grid/Core/Customizer.php';
    require_once get_template_directory() . '/header-footer-grid/Core/Magic_Tags.php';
    require_once get_template_directory() . '/header-footer-grid/Core/Script_Register.php';
    Main::instance()->init();
    \NosfirNews\HeaderFooterGrid\Core\Script_Register::init();
    ( new \NosfirNews\HeaderFooterGrid\Core\Customizer() )->init();
    \nosfirnews_hfg_run_migrations();
}