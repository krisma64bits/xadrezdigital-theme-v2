<?php
/**
 * XD Product Gallery
 * 
 * Galeria de produto baseada no CommerceKit (GPLv3).
 * Usa as mesmas classes CSS e estrutura do plugin original para garantir compatibilidade.
 * Layout fixo: vertical-left (thumbs à esquerda no desktop, horizontal no mobile).
 * 
 * @package XadrezDigital
 * @since 1.0.0
 * @license GPLv3
 * @see https://www.commercegurus.com (código original)
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================
// CONSTANTES
// ============================================
define('XD_GALLERY_VERSION', '1.0.0');

// ============================================
// SETUP: Remove galeria nativa, adiciona customizada
// ============================================
function xd_gallery_setup(): void {
    // Remove galeria nativa do WooCommerce
    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
    
    // Adiciona galeria customizada
    add_action('woocommerce_before_single_product_summary', 'xd_gallery_render', 20);
}
add_action('woocommerce_before_single_product', 'xd_gallery_setup');

/**
 * Remove suporte nativo de galeria (zoom, lightbox, slider)
 * Usamos PhotoSwipe e Swiper no lugar
 */
function xd_gallery_init(): void {
    if (!is_product()) {
        return;
    }
    
    remove_theme_support('wc-product-gallery-lightbox');
    remove_theme_support('wc-product-gallery-zoom');
    remove_theme_support('wc-product-gallery-slider');
}
add_action('wp', 'xd_gallery_init');

// ============================================
// ENQUEUE: Assets (Swiper, PhotoSwipe, JS, CSS)
// ============================================
function xd_gallery_enqueue_assets(): void {
    if (!is_product()) {
        return;
    }
    
    $assets_uri = get_template_directory_uri() . '/assets/xd-gallery/';
    
    // Swiper CSS
    wp_enqueue_style('xd-swiper', $assets_uri . 'css/swiper-bundle.min.css', [], XD_GALLERY_VERSION);
    
    // PhotoSwipe CSS
    wp_enqueue_style('xd-photoswipe', $assets_uri . 'css/photoswipe.min.css', ['xd-swiper'], XD_GALLERY_VERSION);
    wp_enqueue_style('xd-photoswipe-skin', $assets_uri . 'css/default-skin.min.css', ['xd-photoswipe'], XD_GALLERY_VERSION);
    
    // Swiper JS (bundle completo - expõe Swiper662)
    wp_enqueue_script('xd-swiper', $assets_uri . 'js/swiper-bundle.min.js', [], XD_GALLERY_VERSION, true);
    
    // PhotoSwipe JS
    wp_enqueue_script('xd-photoswipe', $assets_uri . 'js/photoswipe.min.js', [], XD_GALLERY_VERSION, true);
    wp_enqueue_script('xd-photoswipe-ui', $assets_uri . 'js/photoswipe-ui-default.min.js', [], XD_GALLERY_VERSION, true);
    
    // Gallery JS (script original do CommerceKit) 
    wp_enqueue_script('xd-gallery', get_template_directory_uri() . '/commercegurus-commercekit/assets/js/commercegurus-gallery.js', ['xd-swiper', 'xd-photoswipe', 'xd-photoswipe-ui'], XD_GALLERY_VERSION, true);
}
add_action('wp_enqueue_scripts', 'xd_gallery_enqueue_assets');

/**
 * Adiciona variáveis JS necessárias para o script original
 */
function xd_gallery_js_variables(): void {
    if (!is_product()) {
        return;
    }
    
    // Configurações fixas (vertical-left, lightbox ativo)
    $config = [
        'pdp_thumbnails'      => 5,
        'pdp_m_thumbs'        => 4,
        'pdp_v_thumbs'        => 5,
        'pdp_lightbox'        => 1,
        'pdp_lightbox_cap'    => 0,
        'pdp_gallery_layout'  => 'vertical-left',
        'pdp_sticky_atc'      => 0,
        'cgkit_sticky_hdr_class' => 'body.sticky-m header.site-header',
        'pdp_mobile_layout'   => 'minimal',
        'pdp_showedge_percent'=> '1.1',
        'pdp_json_data'       => 0,
        'pdp_gal_loaded'      => 0,
    ];
    ?>
    <script type="text/javascript">var commercekit_pdp = <?php echo wp_json_encode($config); ?>;</script>
    <?php
}
add_action('wp_head', 'xd_gallery_js_variables');

// ============================================
// RENDER: Template da galeria (estilo CommerceKit)
// ============================================
function xd_gallery_render(): void {
    global $product;
    
    if (!$product instanceof WC_Product) {
        return;
    }
    
    // Configurações fixas
    $pdp_lightbox = true;
    $pdp_thumbnails = 5;
    $pdp_m_thumbs = 4;
    $vertical_thumbs = 5;
    $cgkit_gallery_layout = 'vertical-left';
    
    $product_id = $product->get_id();
    $post_thumbnail_id = $product->get_image_id();
    $video_gallery = get_post_meta($product_id, 'xd_video_gallery', true);
    
    // Wrapper classes (igual CommerceKit)
    $wrapper_classes = ['woocommerce-product-gallery', 'images'];
    
    // Preparar lista de imagens
    $image_ids = [];
    if ($post_thumbnail_id) {
        $image_ids[] = $post_thumbnail_id;
    }
    $gallery_ids = $product->get_gallery_image_ids();
    if (!empty($gallery_ids)) {
        $image_ids = array_merge($image_ids, $gallery_ids);
    }
    
    $pdp_thub_count = count($image_ids);
    $one_slider_css = $pdp_thub_count <= 1 ? 'cgkit-one-slider' : '';
    
    // Placeholder se não houver imagens
    $placeholder_image = '<li class="swiper-slide" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject"><div class="woocommerce-product-gallery__image--placeholder">' . sprintf('<img src="%s" alt="%s" class="wp-post-image" />', esc_url(wc_placeholder_img_src('woocommerce_single')), esc_html__('Awaiting product image', 'woocommerce')) . '</div></li>';
    
    // CSS Inline (igual CommerceKit - simplificado para vertical-left)
    ?>
<style>
.cg-main-swiper.swiper-container, .cg-thumb-swiper.swiper-container {
    width: 100%;
    height: 100%;
}
.cg-main-swiper ul.swiper-wrapper, .cg-thumb-swiper ul.swiper-wrapper {
    padding: 0;
    margin: 0;
}
.cg-main-swiper .swiper-slide, .cg-thumb-swiper .swiper-slide {
    text-align: center;
    font-size: 18px;
    background: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: auto;
    flex-flow: wrap;
}
.swiper-slide-imglink {
    height: auto;
    width: 100%;
}
.cg-main-swiper {
    height: auto;
    width: 100%;
}
.cg-thumb-swiper {
    height: 20%;
    box-sizing: border-box;
    padding-top: 10px;
}
.cg-thumb-swiper .swiper-slide {
    height: 100%;
    opacity: 0.4;
    cursor: pointer;
}
.cg-thumb-swiper .swiper-slide-thumb-active {
    opacity: 1;
}
.cg-main-swiper .swiper-slide img, .cg-thumb-swiper .swiper-slide img {
    display: block;
    width: 100%;
    height: auto;
}
.cg-main-swiper .swiper-button-next, .cg-main-swiper .swiper-button-prev {
    background-image: none;
}
.cg-main-swiper .swiper-button-next.swiper-button-disabled,
.cg-main-swiper .swiper-button-prev.swiper-button-disabled {
    visibility: hidden;
}
.cg-thumbs-5.cg-thumb-swiper .swiper-slide { width: 20%; }
.cg-thumb-swiper.swiper-container {
    margin-left: -5px;
    width: calc(100% + 10px);
}
.cg-thumb-swiper .swiper-slide {
    padding-left: 5px;
    padding-right: 5px;
    background-color: transparent;
}
.product ul li.swiper-slide {
    margin: 0;
}
/* Lightbox cursor */
.cg-lightbox-active .swiper-slide-imglink {
    cursor: zoom-in;
}
/* SVG Arrows */
#commercegurus-pdp-gallery .swiper-button-next:after,
#commercegurus-pdp-gallery .swiper-button-prev:after {
    content: "";
    font-family: inherit;
    font-size: inherit;
    width: 22px;
    height: 22px;
    background: #111;
    -webkit-mask-position: center;
    -webkit-mask-repeat: no-repeat;
    -webkit-mask-size: contain;
}
#commercegurus-pdp-gallery .swiper-button-next,
#commercegurus-pdp-gallery .swiper-button-prev {
    width: 42px;
    height: 42px;
    margin-top: -21px;
    padding: 0;
    background: hsla(0, 0%, 100%, 0.75);
    transition: background 0.5s ease;
    border-radius: 0.25rem;
    cursor: pointer;
}
#commercegurus-pdp-gallery .swiper-button-next:hover,
#commercegurus-pdp-gallery .swiper-button-prev:hover {
    background: #fff;
}
#commercegurus-pdp-gallery .swiper-button-prev:after,
#commercegurus-pdp-gallery .swiper-button-next:after {
    -webkit-mask-image: url("data:image/svg+xml;charset=utf8,%3Csvg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M15 19L8 12L15 5' stroke='%234A5568' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    mask-image: url("data:image/svg+xml;charset=utf8,%3Csvg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M15 19L8 12L15 5' stroke='%234A5568' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
}
#commercegurus-pdp-gallery .swiper-button-next:after {
    transform: scaleX(-1);
}
/* Hide slides before init */
.swiper-container.cg-main-swiper .swiper-wrapper .swiper-slide {
    display: none;
}
.swiper-container.cg-main-swiper .swiper-wrapper .swiper-slide:first-child {
    display: flex;
}
.swiper-container.cg-main-swiper.swiper-container-initialized .swiper-wrapper .swiper-slide {
    display: flex;
}
/* Vertical Left Layout (Desktop) */
@media (min-width: 771px) {
    .cg-layout-vertical-left {
        display: flex;
    }
    .cg-layout-vertical-left .cg-main-swiper {
        flex: calc(100% - 100px);
        margin-left: 5px;
        margin-right: 0px;
        transition: all 0.1s ease-in;
        order: 2;
    }
    .cg-layout-vertical-left .cg-thumb-swiper {
        flex: 100px;
        padding: 0px;
        order: 1;
    }
    .cg-layout-vertical-left .cg-thumb-swiper .swiper-wrapper {
        display: block;
    }
    .cg-layout-vertical-left .cg-thumb-swiper .swiper-slide {
        width: 100px;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        cursor: pointer;
        overflow: hidden;
        position: relative;
        padding: 2.5px 0 2.5px 5px;
    }
    .cg-layout-vertical-left .cg-thumb-swiper .swiper-slide:first-child {
        padding-top: 0px;
    }
    .cg-layout-vertical-left .cg-thumb-swiper .swiper-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .cg-layout-vertical-left.cgkit-one-slider .cg-main-swiper {
        flex: 100%;
        margin-left: 0px;
        margin-right: 0px;
    }
    .cg-layout-vertical-left.cgkit-one-slider .cg-thumb-swiper {
        flex: 0%;
        margin-left: 0px;
        margin-right: 0px;
    }
    .cg-layout-vertical-left.cgkit-vlayout-<?php echo esc_attr($vertical_thumbs); ?> .cg-thumb-swiper li {
        display: none;
    }
    .cg-layout-vertical-left.cgkit-vlayout-<?php echo esc_attr($vertical_thumbs); ?> .cg-thumb-swiper li:nth-child(-n+<?php echo esc_attr($vertical_thumbs); ?>) {
        display: flex;
    }
}
/* Mobile */
@media (max-width: 770px) {
    .swiper-container.cg-main-swiper .swiper-wrapper .swiper-slide {
        display: none;
    }
    .swiper-container.cg-main-swiper .swiper-wrapper .swiper-slide:first-child {
        display: block;
    }
    .swiper-container.cg-main-swiper.swiper-container-initialized .swiper-wrapper .swiper-slide {
        display: flex;
    }
    .cg-m-thumbs-4.cg-thumb-swiper .swiper-slide { width: 25%; }
}
/* PhotoSwipe button fix */
.pswp button.pswp__button {
    background-color: transparent;
}
</style>

<div id="commercegurus-pdp-gallery-wrapper" class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $wrapper_classes))); ?>">

<div id="commercegurus-pdp-gallery" class="cg-layout-<?php echo esc_attr($cgkit_gallery_layout); ?> cgkit-mb10 cgkit-layout-<?php echo esc_attr($pdp_thumbnails); ?> cgkit-vlayout-<?php echo esc_attr($vertical_thumbs); ?> <?php echo esc_attr($one_slider_css); ?> <?php echo $pdp_lightbox ? 'cg-lightbox-active' : ''; ?>" data-layout-class="cg-layout-<?php echo esc_attr($cgkit_gallery_layout); ?>">
    <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff" class="swiper-container cg-main-swiper">
        <ul class="swiper-wrapper cg-psp-gallery" itemscope itemtype="http://schema.org/ImageGallery">
            <?php
            if (empty($image_ids)) {
                echo $placeholder_image;
            } else {
                foreach ($image_ids as $index => $image_id) {
                    $is_first = ($index === 0);
                    echo xd_gallery_get_main_image_html($image_id, $is_first);
                }
            }
            ?>
        </ul>
        <button class="swiper-button-next" aria-label="Next slide"></button>
        <button class="swiper-button-prev" aria-label="Previous slide"></button>
    </div>
    <div thumbsSlider="" class="swiper-container cg-thumb-swiper cg-thumbs-<?php echo esc_attr($pdp_thumbnails); ?> cg-m-thumbs-<?php echo esc_attr($pdp_m_thumbs); ?> cg-thumbs-count-<?php echo esc_attr($pdp_thub_count); ?>">
        <ul class="swiper-wrapper flex-control-nav" itemscope itemtype="http://schema.org/ImageGallery">
            <?php
            if (!empty($image_ids) && $pdp_thub_count > 1) {
                foreach ($image_ids as $index => $image_id) {
                    $is_first = ($index === 0);
                    echo xd_gallery_get_thumbnail_html($image_id, $is_first, $index);
                }
            }
            ?>
        </ul>
    </div>

</div>
</div>
<div id="cgkit-pdp-gallery-outside" style="height:0px;"></div>
<?php if ($pdp_lightbox): ?>
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true" id="pswp">
    <div class="pswp__bg"></div>
    <div class="pswp__scroll-wrap">
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>
        <div class="pswp__ui pswp__ui--hidden">
            <div class="pswp__top-bar">
                <div class="pswp__counter"></div>
                <button class="pswp__button pswp__button--close" aria-label="Close (Esc)"></button>
                <button class="pswp__button pswp__button--share" aria-label="Share"></button>
                <button class="pswp__button pswp__button--fs" aria-label="Toggle fullscreen"></button>
                <button class="pswp__button pswp__button--zoom" aria-label="Zoom in/out"></button>
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                        <div class="pswp__preloader__cut">
                            <div class="pswp__preloader__donut"></div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
            <div class="pswp__share-tooltip"></div>
        </div>
        <button class="pswp__button pswp__button--arrow--left" aria-label="Previous (arrow left)"></button>
        <button class="pswp__button pswp__button--arrow--right" aria-label="Next (arrow right)">
        </button>
        <div class="pswp__caption">
            <div class="pswp__caption__center"></div>
        </div>
        </div>
    </div>
</div>
<?php endif; ?>
    <?php
}

// ============================================
// HELPERS: Gerar HTML (estilo CommerceKit)
// ============================================

/**
 * Gera HTML do slide principal (igual commercegurus_get_main_gallery_image_html)
 */
function xd_gallery_get_main_image_html(int $attachment_id, bool $main_image = false): string {
    $gallery_thumbnail = wc_get_image_size('gallery_thumbnail');
    $thumbnail_size = apply_filters('woocommerce_gallery_thumbnail_size', [$gallery_thumbnail['width'], $gallery_thumbnail['height']]);
    $image_size = 'woocommerce_single';
    $full_size = apply_filters('woocommerce_gallery_full_size', 'full');
    
    $thumbnail_src = wp_get_attachment_image_src($attachment_id, $thumbnail_size);
    $full_src = wp_get_attachment_image_src($attachment_id, $full_size);
    
    if (!$full_src) {
        return '';
    }
    
    $alt_text = trim(wp_strip_all_tags(get_post_meta($attachment_id, '_wp_attachment_image_alt', true)));
    $img_caption = _wp_specialchars(get_post_field('post_excerpt', $attachment_id), ENT_QUOTES, 'UTF-8', true);
    
    $image = wp_get_attachment_image(
        $attachment_id,
        $image_size,
        false,
        [
            'title'         => _wp_specialchars(get_post_field('post_title', $attachment_id), ENT_QUOTES, 'UTF-8', true),
            'data-caption'  => $img_caption,
            'class'         => 'pdp-img wp-post-image',
            'fetchpriority' => $main_image ? 'high' : 'low',
            'loading'       => $main_image ? 'auto' : 'lazy',
        ]
    );

    return '<li class="woocommerce-product-gallery__image swiper-slide" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
      <a class="swiper-slide-imglink" data-e-disable-page-transition="true" data-elementor-open-lightbox="no" title="' . esc_html__('click to zoom-in', 'woocommerce') . '" href="' . esc_url($full_src[0]) . '" itemprop="contentUrl" data-size="' . esc_attr($full_src[1]) . 'x' . esc_attr($full_src[2]) . '">
        ' . $image . '
      </a>
    </li>';
}

/**
 * Gera HTML do thumbnail (igual commercegurus_get_thumbnail_gallery_image_html)
 */
function xd_gallery_get_thumbnail_html(int $attachment_id, bool $main_image = false, int $index = 0): string {
    $gallery_thumbnail = wc_get_image_size('gallery_thumbnail');
    $thumbnail_size = apply_filters('woocommerce_gallery_thumbnail_size', [$gallery_thumbnail['width'], $gallery_thumbnail['height']]);
    $image_size = 'woocommerce_gallery_thumbnail';
    
    $thumbnail_src = wp_get_attachment_image_src($attachment_id, $thumbnail_size);
    if (!$thumbnail_src) {
        return '';
    }

    $image = wp_get_attachment_image(
        $attachment_id,
        $image_size,
        false,
        [
            'title'        => _wp_specialchars(get_post_field('post_title', $attachment_id), ENT_QUOTES, 'UTF-8', true),
            'data-caption' => _wp_specialchars(get_post_field('post_excerpt', $attachment_id), ENT_QUOTES, 'UTF-8', true),
            'class'        => 'wp-post-image',
        ]
    );

    return '<li class="swiper-slide" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" data-variation-id="' . esc_attr($attachment_id) . '" data-index="' . esc_attr($index) . '">
        ' . $image . '
    </li>';
}
