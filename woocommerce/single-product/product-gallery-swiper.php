<?php
/**
 * Product Gallery with Swiper.js
 * 
 * Template customizado para galeria de produto usando Swiper.js
 * com thumbnails verticais à esquerda.
 * 
 * @package XadrezDigital
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

global $product;

if (!$product instanceof WC_Product) {
    return;
}

$images = xd_get_product_gallery_images($product);

if (empty($images)) {
    // Placeholder se não houver imagens
    echo '<div class="xd-product-gallery">';
    echo wc_placeholder_img('woocommerce_single');
    echo '</div>';
    return;
}

$has_multiple_images = count($images) > 1;
?>

<div class="xd-product-gallery flex flex-col lg:flex-row gap-4"
     data-product-id="<?php echo esc_attr($product->get_id()); ?>">
    
    <?php if ($has_multiple_images) : ?>
    <!-- Thumbnails Swiper (Vertical à esquerda no desktop) -->
    <div class="xd-gallery-thumbs order-2 lg:order-1 lg:w-20 shrink-0">
        <div class="swiper xd-thumbs-swiper" id="xd-thumbs-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($images as $index => $image) : ?>
                <div class="swiper-slide !w-16 !h-16 lg:!w-full lg:!h-auto cursor-pointer rounded-md overflow-hidden border-2 border-transparent transition-colors hover:border-stone-300"
                     data-image-id="<?php echo esc_attr($image['id']); ?>">
                    <img src="<?php echo esc_url($image['thumb_src']); ?>"
                         alt="<?php echo esc_attr($image['alt']); ?>"
                         class="w-full h-full object-cover"
                         loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
                         decoding="async">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Main Image Swiper -->
    <div class="xd-gallery-main flex-1 order-1 lg:order-2 min-w-0">
        <div class="swiper xd-main-swiper rounded-lg overflow-hidden bg-stone-100 aspect-square" id="xd-main-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($images as $index => $image) : ?>
                <div class="swiper-slide"
                     data-image-id="<?php echo esc_attr($image['id']); ?>"
                     data-large-image="<?php echo esc_url($image['full_src']); ?>"
                     data-large-image-width="<?php echo esc_attr($image['full_width']); ?>"
                     data-large-image-height="<?php echo esc_attr($image['full_height']); ?>">
                    <a href="<?php echo esc_url($image['full_src']); ?>"
                       class="xd-gallery-zoom block"
                       data-pswp-width="<?php echo esc_attr($image['full_width']); ?>"
                       data-pswp-height="<?php echo esc_attr($image['full_height']); ?>">
                        <img src="<?php echo esc_url($image['large_src']); ?>"
                             <?php if ($image['srcset']) : ?>
                             srcset="<?php echo esc_attr($image['srcset']); ?>"
                             sizes="(max-width: 768px) 100vw, 50vw"
                             <?php endif; ?>
                             alt="<?php echo esc_attr($image['alt']); ?>"
                             class="w-full h-full object-contain"
                             loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
                             decoding="async">
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($has_multiple_images) : ?>
            <!-- Navigation Arrows -->
            <button class="swiper-button-prev xd-nav-btn !w-10 !h-10 !rounded-full !bg-white/90 !shadow-md after:!text-stone-700 after:!text-sm !left-3 hover:!bg-white transition-colors"
                    aria-label="<?php esc_attr_e('Imagem anterior', 'xadrezdigital'); ?>">
            </button>
            <button class="swiper-button-next xd-nav-btn !w-10 !h-10 !rounded-full !bg-white/90 !shadow-md after:!text-stone-700 after:!text-sm !right-3 hover:!bg-white transition-colors"
                    aria-label="<?php esc_attr_e('Próxima imagem', 'xadrezdigital'); ?>">
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>
