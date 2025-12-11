<?php
/**
 * Shop breadcrumb
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/breadcrumb.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     2.3.0
 * @see         woocommerce_breadcrumb()
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( empty( $breadcrumb ) ) {
    return;
}

?>
<nav class="woocommerce-breadcrumb p-3 border border-stone-200 rounded-lg text-sm text-stone-600 !mb-0" aria-label="<?php esc_attr_e( 'Breadcrumb', 'woocommerce' ); ?>">
    <?php
    $total = count( $breadcrumb );
    foreach ( $breadcrumb as $key => $crumb ) :
        $is_last = ( $key + 1 === $total );
        ?>

        <?php if ( ! empty( $crumb[1] ) && ! $is_last ) : ?>
            <a href="<?php echo esc_url( $crumb[1] ); ?>" class="text-stone-600 hover:text-stone-900 transition-colors">
                <?php echo esc_html( $crumb[0] ); ?>
            </a>
        <?php else : ?>
            <span class="text-stone-900 font-medium"><?php echo esc_html( $crumb[0] ); ?></span>
        <?php endif; ?>

        <?php if ( ! $is_last ) : ?>
            <span class="mx-2 text-stone-400">/</span>
        <?php endif; ?>

    <?php endforeach; ?>
</nav>
