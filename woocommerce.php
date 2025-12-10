<?php
/**
 * WooCommerce Template
 * 
 * Este arquivo é usado para todas as páginas do WooCommerce:
 * - Página de produto único
 * - Arquivos de produtos (categorias, tags, shop)
 * - Resultados de busca de produtos
 * 
 * @package XadrezDigital
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<main class="bg-stone-50 py-8">
    <div class="max-w-7xl mx-auto px-8">
        <?php woocommerce_breadcrumb(); ?>
        <?php woocommerce_content(); ?>
    </div>
</main>

<?php get_footer(); ?>
