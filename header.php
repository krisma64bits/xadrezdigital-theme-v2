<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="bg-white">
    <!-- Header Top -->
    <div class="border-b border-stone-200">
        <div class="max-w-7xl mx-auto px-8 py-4">
            <div class="flex items-center justify-between gap-8">
                <!-- Logo -->
                <div class="flex-shrink-0 w-56">
                    <?php if (has_custom_logo()) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="text-2xl font-bold text-stone-900">
                            <?php bloginfo('name'); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Barra de Pesquisa -->
                <div class="flex-1 max-w-xl">
                    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                            <?php get_template_part('inc/icons/search', null, ['class' => 'w-5 h-5']); ?>
                        </div>
                        <input 
                            type="search" 
                            name="s" 
                            placeholder="Buscar produtos..." 
                            value="<?php echo get_search_query(); ?>"
                            class="w-full bg-white border border-stone-200 rounded-md pl-10 pr-4 py-2 text-sm placeholder:text-stone-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none"
                        >
                        <input type="hidden" name="post_type" value="product">
                    </form>
                </div>

                <!-- Info Actions -->
                <div class="flex items-center divide-x divide-stone-200">
                    <!-- Minha Conta -->
                    <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" class="flex items-center gap-3 px-4 group">
                        <span class="text-stone-700 group-hover:text-amber-600 transition-colors">
                            <?php get_template_part('inc/icons/account', null, ['class' => 'w-7 h-7']); ?>
                        </span>
                        <div class="leading-none">
                            <span class="block text-xs text-stone-500">Olá, Visitante</span>
                            <span class="block text-sm font-medium text-stone-900 group-hover:text-amber-600 transition-colors">Minha Conta</span>
                        </div>
                    </a>

                    <!-- WhatsApp -->
                    <a href="https://wa.me/5511999999999" target="_blank" rel="noopener" class="flex items-center gap-3 px-4 group">
                        <span class="text-emerald-600 group-hover:text-emerald-700 transition-colors">
                            <?php get_template_part('inc/icons/whatsapp', null, ['class' => 'w-7 h-7']); ?>
                        </span>
                        <div class="leading-none">
                            <span class="block text-xs text-stone-500">Comprar via</span>
                            <span class="block text-sm font-medium text-stone-900 group-hover:text-emerald-600 transition-colors">WhatsApp</span>
                        </div>
                    </a>

                    <!-- Ajuda -->
                    <a href="<?php echo esc_url(home_url('/ajuda')); ?>" class="flex items-center gap-3 pl-4 group">
                        <span class="text-stone-700 group-hover:text-amber-600 transition-colors">
                            <?php get_template_part('inc/icons/help', null, ['class' => 'w-7 h-7']); ?>
                        </span>
                        <div class="leading-none">
                            <span class="block text-xs text-stone-500">Precisa de ajuda?</span>
                            <span class="block text-sm font-medium text-stone-900 group-hover:text-amber-600 transition-colors">Central de Ajuda</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Bottom -->
    <div class="bg-stone-900">
        <div class="max-w-7xl mx-auto px-8">
            <div class="flex items-center justify-between">
                <!-- Navegação Principal -->
                <nav class="flex-1 group/nav">
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'primary',
                        'container' => false,
                        'menu_class' => 'flex items-center gap-4',
                        'fallback_cb' => false,
                        'link_before' => '',
                        'link_after' => '',
                        'items_wrap' => '<ul class="%2$s">%3$s</ul>',
                        'walker' => new class extends Walker_Nav_Menu {
                            public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
                                $output .= '<li>';
                                $output .= '<a href="' . esc_url($item->url) . '" class="inline-flex py-3 text-base font-medium text-white transition-all group-hover/nav:opacity-50 hover:!opacity-100 group/link">';
                                $output .= '<span class="relative after:content-[\'\'] after:absolute after:bottom-0 after:right-0 after:h-px after:w-0 after:bg-amber-500 after:transition-all after:duration-300 group-hover/link:after:w-full group-hover/link:after:left-0 group-hover/link:after:right-auto">';
                                $output .= esc_html($item->title);
                                $output .= '</span>';
                            }
                            public function end_el(&$output, $item, $depth = 0, $args = null) {
                                $output .= '</a></li>';
                            }
                        }
                    ]);
                    ?>
                </nav>

                <!-- Carrinho -->
                <?php if (class_exists('WooCommerce')) : ?>
                    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="flex items-center gap-2 hover:opacity-90 transition-opacity group">
                        <span class="text-lg font-bold text-white">
                            <?php echo wp_kses_data(WC()->cart->get_cart_subtotal()); ?>
                        </span>
                        <span class="relative text-white">
                            <?php get_template_part('inc/icons/cart', null, ['class' => 'w-8 h-8']); ?>
                            <span class="absolute -top-1 -right-1 bg-amber-500 text-white text-xs font-bold rounded px-1">
                                <?php echo WC()->cart->get_cart_contents_count(); ?>
                            </span>
                        </span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
