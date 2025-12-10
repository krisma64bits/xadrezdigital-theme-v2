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

    // WooCommerce support
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'xadrez_theme_setup');

function xadrez_enqueue_assets() {
    $manifest_path = get_template_directory() . '/dist/.vite/manifest.json';
    
    if (file_exists($manifest_path)) {
        $manifest = json_decode(file_get_contents($manifest_path), true);
        
        // Carregar CSS compilado
        if (isset($manifest['src/main.ts']['css'])) {
            foreach ($manifest['src/main.ts']['css'] as $css_file) {
                wp_enqueue_style(
                    'xadrez-tailwind',
                    get_template_directory_uri() . '/dist/' . $css_file,
                    [],
                    null
                );
            }
        }
        
        // Carregar JS compilado
        if (isset($manifest['src/main.ts']['file'])) {
            wp_enqueue_script(
                'xadrez-main',
                get_template_directory_uri() . '/dist/' . $manifest['src/main.ts']['file'],
                [],
                null,
                true
            );
        }
    }
    
    // Fallback para o style.css do tema (header do WordPress)
    wp_enqueue_style('xadrez-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'xadrez_enqueue_assets');
