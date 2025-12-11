<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;

if ( ! $product instanceof WC_Product ) {
    return;
}

$regular_price = $product->get_regular_price();
$sale_price = $product->get_sale_price();
$price = $product->get_price();

if ( empty( $price ) ) {
    return;
}

$is_on_sale = $sale_price && $regular_price > $sale_price;
?>

<div class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>">
    <?php if ( $is_on_sale ) : ?>
        <del class="text-stone-500 text-lg font-normal block leading-none" aria-hidden="true">
            <?php echo wc_price( $regular_price ); ?>
        </del>
    <?php endif; ?>

    <span class="text-stone-900 font-bold text-4xl leading-none">
        <?php echo wc_price( $price ); ?>
    </span>
</div>
