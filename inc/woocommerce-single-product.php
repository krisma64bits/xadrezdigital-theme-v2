<?php
/**
 * WooCommerce Single Product Layout Customization
 * Reorganiza as seções da página de produto usando apenas hooks
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ========================================
 * SISTEMA DE ABAS CUSTOMIZADAS
 * ========================================
 */

/**
 * Adiciona classe customizada ao wrapper das tabs via output buffering
 */
add_action('woocommerce_before_single_product', function() {
    ob_start();
});

add_action('woocommerce_after_single_product', function() {
    $html = ob_get_clean();
    
    // ========================================
    // CLASSES INJETADAS VIA OUTPUT BUFFERING
    // ========================================
    
    // Adiciona classe ao container principal do produto
    $html = preg_replace(
        '/class="([^"]*product[^"]*type-product[^"]*)"/',
        'class="$1 xd-single-product"',
        $html,
        1
    );
    
    // Adiciona classe xd-product-tabs ao wrapper .wc-tabs-wrapper
    $html = str_replace(
        'class="woocommerce-tabs wc-tabs-wrapper"',
        'class="woocommerce-tabs wc-tabs-wrapper xd-product-tabs"',
        $html
    );
    
    // Adiciona classe xd-product-section ao wrapper de upsells
    $html = str_replace(
        'class="up-sells upsells products"',
        'class="up-sells upsells products xd-product-section"',
        $html
    );
    
    // Adiciona classe xd-product-section ao wrapper de related products
    $html = str_replace(
        'class="related products"',
        'class="related products xd-product-section"',
        $html
    );
    
    // Adiciona classe xd-product-reviews ao wrapper de reviews
    $html = str_replace(
        'class="woocommerce-Reviews"',
        'class="woocommerce-Reviews xd-product-reviews"',
        $html
    );
    
    echo $html;
});

/**
 * Customiza as abas do produto
 */
add_filter('woocommerce_product_tabs', function($tabs) {
    // Remove tabs padrão
    unset($tabs['description']);
    unset($tabs['additional_information']);
    unset($tabs['reviews']);

    // Aba: Visão Geral (descrição curta/overview)
    $tabs['overview'] = [
        'title' => 'Visão Geral',
        'priority' => 10,
        'callback' => 'xadrez_product_tab_overview'
    ];

    // Aba: Descrição Detalhada
    $tabs['details'] = [
        'title' => 'Descrição Detalhada',
        'priority' => 20,
        'callback' => 'xadrez_product_tab_details'
    ];

    // Aba: Instalação e Entrega
    $tabs['installation'] = [
        'title' => 'Instalação e Entrega',
        'priority' => 30,
        'callback' => 'xadrez_product_tab_installation'
    ];

    return $tabs;
});

/**
 * Callback: Aba Visão Geral
 */
function xadrez_product_tab_overview() {
    global $post;

    echo '<div class="prose max-w-none">';

    // Usa o excerpt/short description do produto
    if ($post->post_excerpt) {
        echo wpautop($post->post_excerpt);
    } else {
        echo '<p>Adicione uma descrição curta ao produto para exibir aqui.</p>';
    }

    echo '</div>';
}

/**
 * Callback: Aba Descrição Detalhada
 */
function xadrez_product_tab_details() {
    // Usa o template padrão do WooCommerce para manter compatibilidade
    wc_get_template('single-product/tabs/description.php');
}

/**
 * Callback: Aba Instalação e Entrega
 */
function xadrez_product_tab_installation() {
    global $post;

    echo '<div class="prose max-w-none">';

    // Você pode usar custom fields aqui
    // Exemplo com ACF: echo get_field('installation_info');
    // Por enquanto, conteúdo placeholder:
    echo '<h3>Instruções de Instalação</h3>';
    echo '<p>Informações sobre instalação serão exibidas aqui.</p>';

    echo '<h3>Informações de Entrega</h3>';
    echo '<p>Informações sobre entrega serão exibidas aqui.</p>';

    echo '</div>';
}

/**
 * ========================================
 * LAYOUT: TABS (2/3) + SIDEBAR (1/3)
 * ========================================
 */

/**
 * Abre container flex ANTES das tabs
 */
add_action('woocommerce_after_single_product_summary', function() {
    echo '<div class="flex flex-col lg:flex-row gap-8">';
    echo '<div class="w-full lg:w-3/5 shrink-0">';
}, 9);

/**
 * Fecha container das tabs e abre sidebar
 */
add_action('woocommerce_after_single_product_summary', function() {
    echo '</div>'; // fecha div das tabs
    echo '<aside class="w-full lg:w-2/5">';

    // Renderiza a sidebar de especificações
    xadrez_product_sidebar_specs();

    echo '</aside>';
    echo '</div>'; // fecha container flex
}, 11);

/**
 * Renderiza sidebar de Especificações Técnicas
 */
function xadrez_product_sidebar_specs() {
    global $product;

    echo '<div class="bg-white border border-stone-200 rounded-lg p-6">';
    echo '<h3 class="text-lg font-semibold text-stone-900 mb-4">Especificações Técnicas</h3>';

    // Attributes do WooCommerce (mantém compatibilidade)
    $attributes = $product->get_attributes();

    if (!empty($attributes)) {
        echo '<dl class="space-y-3">';

        foreach ($attributes as $attribute) {
            $name = wc_attribute_label($attribute->get_name());
            $values = [];

            if ($attribute->is_taxonomy()) {
                $terms = wp_get_post_terms($product->get_id(), $attribute->get_name(), 'all');
                foreach ($terms as $term) {
                    $values[] = $term->name;
                }
            } else {
                $values = $attribute->get_options();
            }

            echo '<div class="grid grid-cols-2 gap-2">';
            echo '<dt class="text-sm font-medium text-stone-700">' . esc_html($name) . '</dt>';
            echo '<dd class="text-sm text-stone-900">' . esc_html(implode(', ', $values)) . '</dd>';
            echo '</div>';
        }

        echo '</dl>';
    } else {
        echo '<p class="text-sm text-stone-500">Nenhuma especificação disponível.</p>';
    }

    echo '</div>';

    // Requisitos (pode adicionar depois)
    echo '<div class="bg-white border border-stone-200 rounded-lg p-6 mt-6">';
    echo '<h3 class="text-lg font-semibold text-stone-900 mb-4">Requisitos do Sistema</h3>';
    echo '<p class="text-sm text-stone-500">Informações de requisitos podem ser adicionadas aqui.</p>';
    echo '</div>';
}

/**
 * ========================================
 * SEÇÕES VERTICAIS (DEPOIS DO LAYOUT FLEX)
 * ========================================
 */

/**
 * Remove upsells e relacionados das posições padrão
 */
remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

/**
 * Renderiza "Você também pode gostar" (Upsells)
 */
add_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 20);

/**
 * Renderiza as avaliações do produto
 */
function xadrez_output_product_reviews() {
    comments_template();
}
add_action('woocommerce_after_single_product_summary', 'xadrez_output_product_reviews', 30);

/**
 * Renderiza produtos relacionados
 */
add_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 40);
