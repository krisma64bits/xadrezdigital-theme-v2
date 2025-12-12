<?php
/**
 * WooCommerce Product Card Badges
 *
 * Adiciona badges de parcelamento e PIX nos cards de produto via hook.
 * Funciona automaticamente em todos os loops: shop, related, upsells, etc.
 *
 * @package XadrezDigital
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Renderiza badges de parcelamento e PIX nos cards de produto
 */
function xd_render_product_card_badges(): void {
    global $product;

    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $price = (float) $product->get_price();

    if ( $price <= 0 ) {
        return;
    }

    // === CONFIGURAÇÕES DE PARCELAMENTO ===
    
    // Parcelas sem juros
    $max_no_interest = apply_filters( 'woocommerce_max_installments_no_interest', 3, $product );
    $min_installment_value = apply_filters( 'woocommerce_min_installment_value', 5.00, $product );
    
    // Parcelas com juros (12x)
    $max_with_interest = 12;
    $interest_rate = apply_filters( 'woocommerce_installment_interest_rate', 0.0199, $product ); // 1.99% ao mês
    
    // Calcula parcelas sem juros
    $no_interest_installments = min( $max_no_interest, (int) floor( $price / $min_installment_value ) );
    $no_interest_value = $no_interest_installments >= 2 ? $price / $no_interest_installments : 0;
    
    // Calcula parcelas com juros (Price - tabela de amortização)
    if ( $interest_rate > 0 && $max_with_interest > 0 ) {
        $with_interest_value = $price * ( $interest_rate * pow( 1 + $interest_rate, $max_with_interest ) ) / ( pow( 1 + $interest_rate, $max_with_interest ) - 1 );
    } else {
        $with_interest_value = $price / $max_with_interest;
    }

    // === CONFIGURAÇÕES DE PIX ===
    $pix_discount_percent = apply_filters( 'woocommerce_pix_discount_percent', 10, $product );
    $pix_discount_percent = max( 0.0, min( 100.0, (float) $pix_discount_percent ) );
    $pix_price = $price * ( 1 - $pix_discount_percent / 100 );

    $show_installments = $no_interest_installments >= 2;
    $show_pix = apply_filters( 'woocommerce_show_pix_discount', $pix_discount_percent > 0, $product );

    if ( ! $show_installments && ! $show_pix ) {
        return;
    }
    ?>
    <div class="xd-product-card-badges">
        <?php if ( $show_installments ) : ?>
            <!-- Parcelamento sem juros -->
            <div class="xd-installment-line">
                <span>
                    <?php
                    printf(
                        /* translators: 1: number of installments, 2: installment value */
                        esc_html__( 'até %1$dx de %2$s sem juros', 'xadrezdigital' ),
                        $no_interest_installments,
                        '<strong>' . wp_kses_post( wc_price( $no_interest_value ) ) . '</strong>'
                    );
                    ?>
                </span>
            </div>
            
            <!-- Parcelamento com juros -->
            <div class="xd-installment-line xd-installment-interest">
                <span>
                    <?php
                    printf(
                        /* translators: 1: number of installments, 2: installment value */
                        esc_html__( 'ou até %1$dx de %2$s com juros', 'xadrezdigital' ),
                        $max_with_interest,
                        '<strong>' . wp_kses_post( wc_price( $with_interest_value ) ) . '</strong>'
                    );
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if ( $show_pix ) : ?>
            <!-- Card PIX -->
            <div class="xd-pix-card">
                <?php get_template_part( 'inc/icons/pix', null, [ 'class' => 'xd-pix-icon' ] ); ?>
                <span class="xd-pix-price"><?php echo wp_kses_post( wc_price( $pix_price ) ); ?></span>
                <span class="xd-pix-label"><?php esc_html_e( 'no pix', 'xadrezdigital' ); ?></span>
                <span class="xd-pix-badge">
                    <?php
                    printf(
                        /* translators: %d: discount percentage */
                        esc_html__( '%d%% OFF', 'xadrezdigital' ),
                        (int) $pix_discount_percent
                    );
                    ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Hook: Adiciona badges após o preço nos loops de produto
 * Prioridade 15 = após preço (prioridade 10)
 */
add_action( 'woocommerce_after_shop_loop_item_title', 'xd_render_product_card_badges', 15 );
