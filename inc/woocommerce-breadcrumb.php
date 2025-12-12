<?php
/**
 * WooCommerce Breadcrumb Customization
 * 
 * Adiciona ícone de casa e estilos via CSS (sem alterar markup).
 * 100% compatível com WooCommerce - zero template override.
 * Cores baseadas em design-tokens.json (stone palette).
 * 
 * @package XadrezDigital
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customiza o breadcrumb do WooCommerce
 * 
 * - Classes Tailwind para layout e tipografia
 * - CSS Inline para o ícone (SVG via data URI é mais seguro via CSS puro que classes arbitrárias)
 */
add_filter('woocommerce_breadcrumb_defaults', function($defaults) {
    // Classes do Container
    $wrapper_classes = [
        'woocommerce-breadcrumb',
        '!flex !items-center !flex-wrap',
        '!m-0 !p-0',
        '!text-sm !text-stone-500',
        // Estilo dos Links (Descendentes) via variantes arbitrárias simples
        '[&_a]:!text-stone-600 [&_a]:no-underline [&_a]:transition-colors [&_a]:duration-150 [&_a]:font-medium',
        '[&_a:hover]:!text-stone-900 [&_a:hover]:underline [&_a:hover]:underline-offset-[0.18em]',
    ];

    // Classes do Separador
    $separator_classes = [
        'breadcrumb-separator',
        '!inline-block !relative !top-0',
        '!px-[0.5em]',
        '!opacity-30',
        '!text-[0.875em]',
        '!text-stone-400'
    ];

    $defaults['delimiter']   = '<span class="' . implode(' ', $separator_classes) . '"> / </span>';
    $defaults['wrap_before'] = '<nav class="' . implode(' ', $wrapper_classes) . '" aria-label="' . esc_attr__('Breadcrumb', 'woocommerce') . '">';
    $defaults['wrap_after']  = '</nav>';

    return $defaults;
});

/**
 * Adiciona CSS específico para o ícone do breadcrumb
 * (Mantido separado para evitar problemas de escape de SVG em classes Tailwind)
 */
add_action('wp_head', function() {
    if (!is_woocommerce() && !is_cart() && !is_checkout()) {
        return;
    }
    ?>
    <style id="xd-breadcrumb-icon">
    /* Ícone de casa no primeiro link */
    .woocommerce-breadcrumb > a:first-child::before {
        content: '' !important;
        display: inline-block !important;
        width: 1.125rem !important;
        height: 1.125rem !important;
        margin-right: 0.35rem !important;
        vertical-align: -0.2em !important;
        background-color: transparent !important;
        background-repeat: no-repeat !important;
        background-position: center !important;
        background-size: contain !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='%2357534e'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'/%3E%3C/svg%3E") !important;
    }
    .woocommerce-breadcrumb > a:first-child:hover::before {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='%231c1917'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'/%3E%3C/svg%3E") !important;
    }
    </style>
    <?php
}, 5);
