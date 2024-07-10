<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once get_stylesheet_directory() . '/class-theme-setup.php';
require_once get_stylesheet_directory() . '/class-jetengine-options.php';

new Theme_Setup();

function display_jetengine_data_shortcode() {
    $jet_engine_options = new JetEngine_Options();

    $introductions = $jet_engine_options->get_introductions();
    $all_repeaters = $jet_engine_options->get_all_repeaters();

    ob_start();

    echo '<pre>';
    echo "Introduções:\n";
    print_r($introductions);

    echo "\nRepeaters:\n";
    print_r($all_repeaters);
    echo '</pre>';

    return ob_get_clean();
}

add_shortcode('display_jetengine_data', 'display_jetengine_data_shortcode');
