<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once get_stylesheet_directory() . '/class-theme-setup.php';

new Theme_Setup();
