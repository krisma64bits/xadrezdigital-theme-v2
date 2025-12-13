<?php
/**
 * WooCommerce Price Display Customization
 * 
 * Segue Regra 16: Hooks + CSS, sem alterar markup do WooCommerce.
 * - Preço padrão mantido (estilizado via CSS)
 * - Parcelamento, PIX e botão adicionados via hooks
 * 
 * @package XadrezDigital
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Adiciona funcionalidades extras DEPOIS do preço padrão
 * Hook: woocommerce_single_product_summary
 * Prioridade: 11 (depois do preço que é 10)
 */
add_action('woocommerce_single_product_summary', function() {
    global $product;
    
    if (!$product instanceof WC_Product) {
        return;
    }
    
    $price = (float) $product->get_price();
    
    if ($price <= 0) {
        return;
    }
    
    // 1. Parcelamento
    xd_render_installments($product, $price);
    
    // 2. Botão "Ver mais opções"
    xd_render_payment_options_button($product);
    
    // 3. PIX Discount
    xd_render_pix_discount($product, $price);
    
    
}, 11);

/**
 * Renderiza informações de parcelamento
 */
function xd_render_installments($product, $price) {
    /**
     * Filtro para número máximo de parcelas
     * Plugins de pagamento podem modificar este valor
     */
    $max_installments = apply_filters('woocommerce_max_installments', 12, $product);
    
    /**
     * Filtro para valor mínimo da parcela
     */
    $min_installment_value = apply_filters('woocommerce_min_installment_value', 5.00, $product);
    
    // Calcula o número real de parcelas respeitando o valor mínimo
    $calculated_installments = min(
        $max_installments,
        (int) floor($price / $min_installment_value)
    );
    
    if ($calculated_installments < 2) {
        return;
    }
    
    $installment_value = $price / $calculated_installments;
    ?>
    <div class="xd-installments flex items-center gap-2 text-stone-700 mt-1">
        <?php get_template_part('inc/icons/credit-card', null, ['class' => 'w-4 h-4 text-stone-700']); ?>
        <span class="text-sm">
            <?php
            printf(
                /* translators: 1: number of installments, 2: installment value */
                esc_html__('em até %1$dx de %2$s', 'xadrezdigital'),
                $calculated_installments,
                '<strong>' . wp_kses_post(wc_price($installment_value)) . '</strong>'
            );
            ?>
        </span>
    </div>
    <?php
}

/**
 * Renderiza card de desconto PIX
 */
function xd_render_pix_discount($product, $price) {
    /**
     * Filtro para percentual de desconto PIX
     * Plugins de pagamento PIX podem modificar este valor
     */
    $pix_discount_percent = apply_filters('woocommerce_pix_discount_percent', 10, $product);
    $pix_discount_percent = max(0.0, min(100.0, (float) $pix_discount_percent));
    
    /**
     * Filtro para habilitar/desabilitar card PIX
     */
    if (!apply_filters('woocommerce_show_pix_discount', $pix_discount_percent > 0, $product)) {
        return;
    }
    
    $pix_price = $price * (1 - $pix_discount_percent / 100);
    $savings = $price - $pix_price;
    ?>
    <div class="xd-pix-discount inline-flex items-center px-3 py-2 mt-3 rounded-md border border-stone-200 bg-white">
        <?php get_template_part('inc/icons/pix', null, ['class' => 'w-8 h-8 text-emerald-600 flex-shrink-0']); ?>
        
        <div class="flex flex-col ml-3 leading-tight min-w-0">
            <div class="flex items-center gap-2 leading-tight flex-wrap">
                <span class="xd-pix-price text-lg font-bold leading-tight">
                    <?php echo wp_kses_post(wc_price($pix_price)); ?>
                </span>
                <span class="xd-pix-badge text-xs bg-emerald-600 text-white px-2 py-1 rounded leading-tight">
                    <?php
                    printf(
                        /* translators: %d: discount percentage */
                        esc_html__('%d%% OFF', 'xadrezdigital'),
                        (int) $pix_discount_percent
                    );
                    ?>
                </span>
            </div>
            <span class="text-sm text-stone-600 leading-tight">
                <?php
                printf(
                    /* translators: %s: savings amount */
                    esc_html__('Pague com pix e economize %s', 'xadrezdigital'),
                    wp_kses_post(wc_price($savings))
                );
                ?>
            </span>
        </div>
    </div>
    <?php
}

/**
 * Renderiza botão de opções de pagamento
 */
function xd_render_payment_options_button($product) {
    /**
     * Filtro para habilitar/desabilitar botão de opções de pagamento
     */
    if (!apply_filters('woocommerce_show_payment_options_button', true, $product)) {
        return;
    }
    ?>
    <button
        type="button"
        class="xd-payment-options-btn inline-flex items-center justify-center rounded-md px-4 py-1 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-stone-200 bg-transparent hover:bg-stone-100 text-stone-900 focus-visible:ring-stone-950 mt-2"
        data-product-id="<?php echo esc_attr($product->get_id()); ?>"
    >
        <?php esc_html_e('Ver mais opções de pagamento', 'xadrezdigital'); ?>
    </button>
    <?php
}
