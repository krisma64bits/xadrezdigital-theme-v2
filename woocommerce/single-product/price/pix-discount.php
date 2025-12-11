<?php
/**
 * Single Product PIX Discount
 *
 * Exibe card de desconto para pagamento via PIX.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     XadrezDigital\Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;

if ( ! $product instanceof WC_Product ) {
    return;
}

$price = (float) $product->get_price();

if ( $price <= 0 ) {
    return;
}

/**
 * Filtro para percentual de desconto PIX
 * Plugins de pagamento PIX podem modificar este valor
 *
 * @param float      $discount_percent Percentual de desconto (0-100)
 * @param WC_Product $product          Produto atual
 */
$pix_discount_percent = apply_filters( 'woocommerce_pix_discount_percent', 10, $product );
$pix_discount_percent = max( 0.0, min( 100.0, (float) $pix_discount_percent ) );

/**
 * Filtro para habilitar/desabilitar card PIX
 *
 * @param bool       $show_pix Se deve exibir o card PIX
 * @param WC_Product $product  Produto atual
 */
if ( ! apply_filters( 'woocommerce_show_pix_discount', $pix_discount_percent > 0, $product ) ) {
    return;
}

$pix_price = $price * ( 1 - $pix_discount_percent / 100 );
$savings = $price - $pix_price;
?>

<div class="xd-pix-discount inline-flex items-center px-3 py-2 mt-3 rounded-md border border-stone-200 bg-white">
    <?php get_template_part( 'inc/icons/pix', null, [ 'class' => 'w-8 h-8 text-emerald-600 flex-shrink-0' ] ); ?>

    <div class="flex flex-col ml-3 leading-tight min-w-0">
        <div class="flex items-center gap-2 leading-tight flex-wrap">
            <span class="xd-pix-price text-lg font-bold leading-tight">
                <?php echo wp_kses_post( wc_price( $pix_price ) ); ?>
            </span>
            <span class="xd-pix-badge text-xs bg-emerald-600 text-white px-2 py-1 rounded leading-tight">
                <?php
                printf(
                    /* translators: %d: discount percentage */
                    esc_html__( '%d%% OFF', 'xadrezdigital' ),
                    (int) $pix_discount_percent
                );
                ?>
            </span>
        </div>
        <span class="text-sm text-stone-600 leading-tight">
            <?php
            printf(
                /* translators: %s: savings amount */
                esc_html__( 'Pague com pix e economize %s', 'xadrezdigital' ),
                wp_kses_post( wc_price( $savings ) )
            );
            ?>
        </span>
    </div>
</div>
