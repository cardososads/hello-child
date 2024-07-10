<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once get_stylesheet_directory() . '/class-theme-setup.php';
require_once get_stylesheet_directory() . '/class-jetengine-options.php';

new Theme_Setup();

function display_jetengine_data_shortcode() {
    $audios = get_option('_audios');
    var_dump($audios);
}

add_shortcode('display_jetengine_data', 'display_jetengine_data_shortcode');
