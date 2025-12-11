<?php
/**
 * WooCommerce Product Price Extensions
 *
 * Registra hooks para adicionar elementos extras ao summary do produto.
 * Os templates estão em woocommerce/single-product/price/
 *
 * Templates:
 * - woocommerce/single-product/price/installments.php
 * - woocommerce/single-product/price/payment-options.php
 * - woocommerce/single-product/price/pix-discount.php
 *
 * Hooks utilizados:
 * - woocommerce_single_product_summary (prioridades 11, 12, 13)
 * - woocommerce_structured_data_product_offer (SEO)
 *
 * @package XadrezDigital
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Exibe informações de parcelamento após o preço
 * Prioridade 11 = logo após o preço (prioridade 10)
 */
function xd_render_installments_info(): void {
    wc_get_template( 'single-product/price/installments.php' );
}
add_action( 'woocommerce_single_product_summary', 'xd_render_installments_info', 11 );

/**
 * Exibe botão de opções de pagamento
 * Prioridade 12 = após parcelamento
 */
function xd_render_payment_options(): void {
    wc_get_template( 'single-product/price/payment-options.php' );
}
add_action( 'woocommerce_single_product_summary', 'xd_render_payment_options', 12 );

/**
 * Exibe card de desconto PIX
 * Prioridade 13 = após botão de pagamento
 */
function xd_render_pix_discount(): void {
    wc_get_template( 'single-product/price/pix-discount.php' );
}
add_action( 'woocommerce_single_product_summary', 'xd_render_pix_discount', 13 );

/**
 * Atualiza structured data para refletir preço PIX quando aplicável
 * Mantém compatibilidade com SEO e Google Rich Snippets
 *
 * @param array      $markup  Array de dados estruturados
 * @param WC_Product $product Produto atual
 * @return array Dados estruturados modificados
 */
function xd_structured_data_pix_price( array $markup, WC_Product $product ): array {
    /**
     * Filtro para incluir preço PIX no schema
     *
     * @param bool       $include_pix_in_schema Se deve incluir preço PIX no schema
     * @param WC_Product $product               Produto atual
     */
    if ( ! apply_filters( 'woocommerce_include_pix_price_in_schema', false, $product ) ) {
        return $markup;
    }

    $price = (float) $product->get_price();

    if ( $price <= 0 ) {
        return $markup;
    }

    $pix_discount_percent = apply_filters( 'woocommerce_pix_discount_percent', 10, $product );
    $pix_discount_percent = max( 0.0, min( 100.0, (float) $pix_discount_percent ) );

    if ( $pix_discount_percent <= 0 ) {
        return $markup;
    }

    $pix_price = $price * ( 1 - $pix_discount_percent / 100 );

    // Adiciona priceSpecification para preço PIX
    $markup['priceSpecification'] = [
        '@type'                 => 'UnitPriceSpecification',
        'price'                 => $pix_price,
        'priceCurrency'         => get_woocommerce_currency(),
        'valueAddedTaxIncluded' => wc_prices_include_tax(),
        'description'           => __( 'Preço com desconto PIX', 'xadrezdigital' ),
    ];

    return $markup;
}
add_filter( 'woocommerce_structured_data_product_offer', 'xd_structured_data_pix_price', 10, 2 );
