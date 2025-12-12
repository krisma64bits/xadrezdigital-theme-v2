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

    // Custom Logo support
    add_theme_support('custom-logo', [
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
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
    
    if (!file_exists($manifest_path)) {
        // Fallback para o style.css do tema (header do WordPress)
        wp_enqueue_style('xadrez-style', get_stylesheet_uri());
        return;
    }
    
    $manifest = json_decode(file_get_contents($manifest_path), true);
    $dist_uri = get_template_directory_uri() . '/dist/';
    
    // CSS global (main.ts)
    if (isset($manifest['src/main.ts']['css'])) {
        foreach ($manifest['src/main.ts']['css'] as $css_file) {
            wp_enqueue_style('xadrez-main', $dist_uri . $css_file, [], null);
        }
    }
    
    // JS global (main.ts) - opcional, só carrega se tiver conteúdo além de CSS
    if (isset($manifest['src/main.ts']['file'])) {
        wp_enqueue_script('xadrez-main', $dist_uri . $manifest['src/main.ts']['file'], [], null, true);
    }
    
    // CSS/JS específico de Single Product - CONDICIONAL
    if (function_exists('is_product') && is_product()) {
        // CSS do single-product
        if (isset($manifest['src/pages/single-product.ts']['css'])) {
            foreach ($manifest['src/pages/single-product.ts']['css'] as $css_file) {
                wp_enqueue_style('xadrez-single-product', $dist_uri . $css_file, ['xadrez-main'], null);
            }
        }
        
        // JS do single-product
        if (isset($manifest['src/pages/single-product.ts']['file'])) {
            wp_enqueue_script(
                'xadrez-single-product',
                $dist_uri . $manifest['src/pages/single-product.ts']['file'],
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

// WooCommerce Custom Gallery
require_once get_template_directory() . '/inc/woocommerce-gallery.php';

// WooCommerce Custom Price Display
require_once get_template_directory() . '/inc/woocommerce-price.php';

// WooCommerce Product Card Badges (parcelamento/PIX nos loops)
require_once get_template_directory() . '/inc/woocommerce-product-badges.php';

// WooCommerce Single Product Layout
require_once get_template_directory() . '/inc/woocommerce-single-product.php';
