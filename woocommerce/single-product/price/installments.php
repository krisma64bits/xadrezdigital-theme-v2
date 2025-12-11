<?php
/**
 * Single Product Installments
 *
 * Exibe informações de parcelamento do produto.
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
 * Filtro para número máximo de parcelas
 * Plugins de pagamento podem modificar este valor
 *
 * @param int        $max_installments Número máximo de parcelas
 * @param WC_Product $product          Produto atual
 */
$max_installments = apply_filters( 'woocommerce_max_installments', 12, $product );

/**
 * Filtro para valor mínimo da parcela
 *
 * @param float      $min_installment_value Valor mínimo da parcela
 * @param WC_Product $product               Produto atual
 */
$min_installment_value = apply_filters( 'woocommerce_min_installment_value', 5.00, $product );

// Calcula o número real de parcelas respeitando o valor mínimo
$calculated_installments = min(
    $max_installments,
    (int) floor( $price / $min_installment_value )
);

if ( $calculated_installments < 2 ) {
    return;
}

$installment_value = $price / $calculated_installments;
?>

<div class="xd-installments flex items-center gap-2 text-stone-700 mt-1">
    <?php get_template_part( 'inc/icons/credit-card', null, [ 'class' => 'w-4 h-4 text-stone-700' ] ); ?>
    <span class="text-sm">
        <?php
        printf(
            /* translators: 1: number of installments, 2: installment value */
            esc_html__( 'em até %1$dx de %2$s', 'xadrezdigital' ),
            $calculated_installments,
            '<strong>' . wp_kses_post( wc_price( $installment_value ) ) . '</strong>'
        );
        ?>
    </span>
</div>
