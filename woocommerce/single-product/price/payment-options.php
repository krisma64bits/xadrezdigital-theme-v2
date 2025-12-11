<?php
/**
 * Single Product Payment Options Button
 *
 * Exibe botão para ver mais opções de pagamento.
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

/**
 * Filtro para habilitar/desabilitar botão de opções de pagamento
 *
 * @param bool       $show_button Se deve exibir o botão
 * @param WC_Product $product     Produto atual
 */
if ( ! apply_filters( 'woocommerce_show_payment_options_button', true, $product ) ) {
    return;
}
?>

<button
    type="button"
    class="xd-payment-options-btn inline-flex items-center justify-center rounded-md px-4 py-1 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-stone-200 bg-transparent hover:bg-stone-100 text-stone-900 focus-visible:ring-stone-950 mt-2"
    data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
>
    <?php esc_html_e( 'Ver mais opções de pagamento', 'xadrezdigital' ); ?>
</button>
