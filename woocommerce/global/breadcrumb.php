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
<nav class="woocommerce-breadcrumb flex !mb-0" aria-label="<?php esc_attr_e( 'Breadcrumb', 'woocommerce' ); ?>">
    <ol class="inline-flex items-center space-x-1 md:space-x-2">
        <?php
        $total = count( $breadcrumb );
        foreach ( $breadcrumb as $key => $crumb ) :
            $is_first = ( $key === 0 );
            $is_last = ( $key + 1 === $total );
            ?>

            <?php if ( $is_first ) : ?>
                <li class="inline-flex items-center">
                    <?php if ( ! empty( $crumb[1] ) && ! $is_last ) : ?>
                        <a href="<?php echo esc_url( $crumb[1] ); ?>" class="inline-flex items-center text-sm font-medium text-stone-600 hover:text-stone-900 hover:underline transition-colors">
                            <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"/>
                            </svg>
                            <?php echo esc_html( $crumb[0] ); ?>
                        </a>
                    <?php else : ?>
                        <span class="inline-flex items-center text-sm font-medium text-stone-500">
                            <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"/>
                            </svg>
                            <?php echo esc_html( $crumb[0] ); ?>
                        </span>
                    <?php endif; ?>
                </li>
            <?php else : ?>
                <li<?php echo $is_last ? ' aria-current="page"' : ''; ?>>
                    <div class="flex items-center space-x-1.5">
                        <svg class="w-3.5 h-3.5 text-stone-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
                        </svg>
                        <?php if ( ! empty( $crumb[1] ) && ! $is_last ) : ?>
                            <a href="<?php echo esc_url( $crumb[1] ); ?>" class="inline-flex items-center text-sm font-medium text-stone-600 hover:text-stone-900 hover:underline transition-colors">
                                <?php echo esc_html( $crumb[0] ); ?>
                            </a>
                        <?php else : ?>
                            <span class="inline-flex items-center text-sm font-medium text-stone-500">
                                <?php echo esc_html( $crumb[0] ); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endif; ?>

        <?php endforeach; ?>
    </ol>
</nav>
