<?php
/**
 * WooCommerce Custom Product Gallery with Swiper.js
 * 
 * Substitui a galeria padrão do WooCommerce por uma galeria customizada
 * usando Swiper.js com thumbnails verticais à esquerda.
 * 
 * @package XadrezDigital
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Remove a galeria padrão do WooCommerce e adiciona a customizada
 */
function xd_setup_custom_gallery(): void {
    // Remove galeria padrão
    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
    
    // Adiciona galeria customizada
    add_action('woocommerce_before_single_product_summary', 'xd_render_product_gallery', 20);
    
    // Abre container flex ANTES da galeria (menor prioridade que a galeria)
    add_action('woocommerce_before_single_product_summary', 'xd_open_product_layout', 5);
    
    // Fecha container flex DEPOIS do summary
    add_action('woocommerce_after_single_product_summary', 'xd_close_product_layout', 5);
}
add_action('wp', 'xd_setup_custom_gallery');

/**
 * Abre o container flex para galeria + summary
 */
function xd_open_product_layout(): void {
    echo '<div class="xd-product-layout flex flex-col lg:flex-row lg:gap-8">';
}

/**
 * Fecha o container flex
 */
function xd_close_product_layout(): void {
    echo '</div>';
}

/**
 * Renderiza a galeria customizada do produto
 */
function xd_render_product_gallery(): void {
    global $product;
    
    if (!$product instanceof WC_Product) {
        return;
    }
    
    wc_get_template(
        'single-product/product-gallery-swiper.php',
        [
            'product' => $product,
        ]
    );
}

/**
 * Obtém os dados de uma imagem para a galeria
 * 
 * @param int $image_id ID do attachment
 * @return array|null Dados da imagem ou null se inválido
 */
function xd_get_gallery_image_data(int $image_id): ?array {
    if (!$image_id) {
        return null;
    }
    
    $full_src = wp_get_attachment_image_url($image_id, 'full');
    $large_src = wp_get_attachment_image_url($image_id, 'woocommerce_single');
    $thumb_src = wp_get_attachment_image_url($image_id, 'woocommerce_gallery_thumbnail');
    
    if (!$full_src || !$large_src || !$thumb_src) {
        return null;
    }
    
    $full_image = wp_get_attachment_image_src($image_id, 'full');
    $srcset = wp_get_attachment_image_srcset($image_id, 'woocommerce_single');
    $alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
    
    // Fallback para título do produto se não tiver alt
    if (empty($alt)) {
        $alt = get_the_title();
    }
    
    return [
        'id'           => $image_id,
        'full_src'     => $full_src,
        'large_src'    => $large_src,
        'thumb_src'    => $thumb_src,
        'srcset'       => $srcset ?: '',
        'alt'          => $alt,
        'full_width'   => $full_image[1] ?? 0,
        'full_height'  => $full_image[2] ?? 0,
    ];
}

/**
 * Obtém todas as imagens da galeria de um produto
 * 
 * @param WC_Product $product Produto WooCommerce
 * @return array Array de dados das imagens
 */
function xd_get_product_gallery_images(WC_Product $product): array {
    $images = [];
    
    // Imagem principal
    $main_image_id = $product->get_image_id();
    if ($main_image_id) {
        $main_image = xd_get_gallery_image_data($main_image_id);
        if ($main_image) {
            $main_image['is_main'] = true;
            $images[] = $main_image;
        }
    }
    
    // Imagens da galeria
    $gallery_ids = $product->get_gallery_image_ids();
    foreach ($gallery_ids as $gallery_id) {
        $gallery_image = xd_get_gallery_image_data($gallery_id);
        if ($gallery_image) {
            $gallery_image['is_main'] = false;
            $images[] = $gallery_image;
        }
    }
    
    return $images;
}
