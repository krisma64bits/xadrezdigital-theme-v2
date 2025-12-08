<?php
/**
 * Xadrez Digital Theme Functions
 */

if (!defined('ABSPATH')) {
    exit;
}

function xadrez_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');

    register_nav_menus([
        'primary' => __('Menu Principal', 'xadrezdigital'),
    ]);
}
add_action('after_setup_theme', 'xadrez_theme_setup');

function xadrez_enqueue_assets() {
    wp_enqueue_style('xadrez-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'xadrez_enqueue_assets');
