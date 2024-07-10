<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once get_stylesheet_directory() . '/class-theme-setup.php';
require_once get_stylesheet_directory() . '/class-jetengine-options.php';

new Theme_Setup();

class JetEngine_Shortcode {
    private $jet_engine_options;

    public function __construct() {
        $this->jet_engine_options = new JetEngine_Options('_audios');
        add_shortcode('display_jetengine_data', [$this, 'display_data_shortcode']);
    }

    public function display_data_shortcode($atts) {
        $introductions = $this->jet_engine_options->get_introductions();
        $all_repeaters = $this->jet_engine_options->get_all_repeaters();

        ob_start();

        echo '<pre>';
        echo "Introduções:\n";
        print_r($introductions);

        echo "\nRepeaters:\n";
        print_r($all_repeaters);
        echo '</pre>';

        return ob_get_clean();
    }
}

new JetEngine_Shortcode();
