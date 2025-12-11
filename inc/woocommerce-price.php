<?php
/**
 * WooCommerce Custom Product Price Display
 *
 * Personaliza a exibição de preços no single product, incluindo:
 * - Display customizado de preço com destaque
 * - Informações de parcelamento
 * - Botão de opções de pagamento
 * - Card de desconto PIX
 *
 * @package XadrezDigital
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configura os hooks customizados de preço
 */
function xd_setup_custom_price(): void {
    // Remove o título padrão do WooCommerce
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);

    // Remove o preço padrão do WooCommerce
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);

    // Remove a descrição curta padrão
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);

    // Adiciona nosso título customizado
    add_action('woocommerce_single_product_summary', 'xd_render_custom_title', 5);

    // Adiciona nosso display customizado de preço
    add_action('woocommerce_single_product_summary', 'xd_render_custom_price', 10);

    // Adiciona descrição curta escondida (para SEO)
    add_action('woocommerce_single_product_summary', 'xd_render_hidden_excerpt', 20);
}
add_action('wp', 'xd_setup_custom_price');

/**
 * Renderiza o título customizado do produto
 */
function xd_render_custom_title(): void {
    echo '<h1 class="text-stone-900 text-3xl font-bold mb-2">';
    the_title();
    echo '</h1>';
}

/**
 * Renderiza a descrição curta escondida (mantém para SEO)
 */
function xd_render_hidden_excerpt(): void {
    global $post;

    if (!$post || empty($post->post_excerpt)) {
        return;
    }

    echo '<div class="hidden">';
    echo '<div class="woocommerce-product-details__short-description">';
    echo wpautop(do_shortcode($post->post_excerpt));
    echo '</div>';
    echo '</div>';
}

/**
 * Renderiza o display customizado de preço completo
 */
function xd_render_custom_price(): void {
    global $product;

    if (!$product instanceof WC_Product) {
        return;
    }

    echo '<div class="xd-product-price-wrapper">';

    xd_render_price_main($product);
    xd_render_installments($product);
    xd_render_payment_options_button();
    xd_render_pix_discount_card($product);

    echo '</div>';
}

/**
 * Renderiza o bloco principal de preço
 *
 * @param WC_Product $product Produto WooCommerce
 */
function xd_render_price_main(WC_Product $product): void {
    $regular_price = $product->get_regular_price();
    $sale_price = $product->get_sale_price();
    $price = $product->get_price();

    if (empty($price)) {
        return;
    }

    // Preço original (se estiver em promoção)
    if ($sale_price && $regular_price > $sale_price) {
        echo '<div class="text-stone-500 line-through text-md">';
        echo wc_price($regular_price);
        echo '</div>';
    }

    // Preço atual
    echo '<div class="text-stone-900 font-bold text-4xl">';
    echo wc_price($price);
    echo '</div>';
}

/**
 * Renderiza informações de parcelamento
 *
 * @param WC_Product $product Produto WooCommerce
 */
function xd_render_installments(WC_Product $product): void {
    $price = $product->get_price();

    if (empty($price)) {
        return;
    }

    // Configuração de parcelamento (pode ser filtrado)
    $max_installments = apply_filters('xd_max_installments', 12);
    $installment_value = $price / $max_installments;

    echo '<div class="flex items-center gap-2 text-stone-700">';

    // Ícone de cartão
    get_template_part('inc/icons/credit-card', null, ['class' => 'w-4 h-4 text-stone-700']);

    echo '<span class="text-sm">';
    echo sprintf(
        'em até %dx de %s',
        $max_installments,
        '<strong>' . wc_price($installment_value) . '</strong>'
    );
    echo '</span>';

    echo '</div>';
}

/**
 * Renderiza botão de ver mais opções de pagamento
 */
function xd_render_payment_options_button(): void {
    echo '<button type="button" class="inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-stone-200 bg-transparent hover:bg-stone-100 text-stone-900 focus-visible:ring-stone-950 mt-2">';
    echo 'Ver mais opções de pagamento';
    echo '</button>';
}

/**
 * Renderiza card de desconto PIX
 *
 * @param WC_Product $product Produto WooCommerce
 */
function xd_render_pix_discount_card(WC_Product $product): void {
    $price = $product->get_price();

    if (empty($price)) {
        return;
    }

    // Percentual de desconto PIX (pode ser filtrado ou configurado via meta)
    $pix_discount_percent = apply_filters('xd_pix_discount_percent', 10);
    $pix_discount_percent = max(0, min(100, $pix_discount_percent));

    if ($pix_discount_percent <= 0) {
        return;
    }

    $pix_price = $price * (1 - $pix_discount_percent / 100);
    $savings = $price - $pix_price;

    echo '<div class="inline-flex items-center px-3 py-2 mt-2 rounded-md border border-stone-200 bg-white">';

    // Ícone PIX
    get_template_part('inc/icons/pix', null, ['class' => 'w-8 h-8 text-emerald-600 flex-shrink-0']);

    // Conteúdo
    echo '<div class="flex flex-col ml-3 leading-tight min-w-0">';

    // Linha 1: Preço + Badge
    echo '<div class="flex items-center gap-2 leading-tight flex-wrap">';
    echo '<span class="text-lg font-bold leading-tight">';
    echo wc_price($pix_price);
    echo '</span>';
    echo '<span class="text-xs bg-emerald-600 text-white px-2 py-1 rounded leading-tight">';
    echo sprintf('%d%% OFF', $pix_discount_percent);
    echo '</span>';
    echo '</div>';

    // Linha 2: Texto de economia
    echo '<span class="text-sm text-stone-600 leading-tight">';
    echo sprintf(
        'Pague com pix e economize %s',
        wc_price($savings)
    );
    echo '</span>';

    echo '</div>'; // .flex-col

    echo '</div>'; // .flex
}

/**
 * Obtém o percentual de desconto PIX de um produto
 * Busca primeiro em meta field, depois usa filtro global
 *
 * @param WC_Product $product Produto WooCommerce
 * @return float Percentual de desconto (0-100)
 */
function xd_get_pix_discount_percent(WC_Product $product): float {
    // Tenta pegar de meta field específico do produto
    $product_discount = $product->get_meta('_xd_pix_discount_percent');

    if ($product_discount !== '') {
        return (float) $product_discount;
    }

    // Fallback para configuração global via filtro
    return (float) apply_filters('xd_pix_discount_percent', 10);
}
