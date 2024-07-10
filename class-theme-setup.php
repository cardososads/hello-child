<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Theme_Setup {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('after_setup_theme', [$this, 'setup_theme']);
    }

    public function enqueue_styles() {
        // Carregar o estilo do tema pai
        wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
        // Carregar o estilo do tema filho
        wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', ['parent-style']);
    }

    public function setup_theme() {
        // Suporte a thumbnails
        add_theme_support('post-thumbnails');

        // Suporte a título dinâmico
        add_theme_support('title-tag');

        // Suporte a logo customizada
        add_theme_support('custom-logo');

        // Suporte a menus
        register_nav_menus([
            'primary' => __('Primary Menu', 'hello-child'),
        ]);
    }
}
