/**
 * Product Gallery - WooCommerce Variations Support
 */

import type Swiper from 'swiper';

/**
 * Configura suporte para variações de produto WooCommerce
 */
export function setupVariationSupport(mainSwiper: Swiper, thumbsSwiper: Swiper | null): void {
    const form = document.querySelector<HTMLFormElement>('form.variations_form');

    if (!form) {
        return;
    }

    // Escuta mudança de variação
    form.addEventListener('found_variation', ((event: CustomEvent) => {
        const variation = event.detail;

        if (variation?.image?.full_src) {
            updateMainImage(mainSwiper, variation.image);
            updateThumbnail(thumbsSwiper, variation.image);
            mainSwiper.slideTo(0);
        }
    }) as EventListener);

    // Escuta reset de imagem
    form.addEventListener('reset_image', () => {
        mainSwiper.slideTo(0);
    });
}

/**
 * Atualiza a imagem principal com a imagem da variação
 */
function updateMainImage(swiper: Swiper, image: VariationImage): void {
    const firstSlide = swiper.slides[0];
    const img = firstSlide?.querySelector('img');
    const link = firstSlide?.querySelector('a');

    if (img && image.src) {
        img.src = image.src;
        if (image.srcset) {
            img.srcset = image.srcset;
        }
    }

    if (link && image.full_src) {
        link.href = image.full_src;
        link.dataset.pswpWidth = image.full_src_w?.toString();
        link.dataset.pswpHeight = image.full_src_h?.toString();
    }
}

/**
 * Atualiza o thumbnail com a imagem da variação
 */
function updateThumbnail(swiper: Swiper | null, image: VariationImage): void {
    if (!swiper) return;

    const firstThumb = swiper.slides[0];
    const thumbImg = firstThumb?.querySelector('img');

    if (thumbImg && image.thumb_src) {
        thumbImg.src = image.thumb_src;
    }
}

/**
 * Tipo para imagem de variação do WooCommerce
 */
interface VariationImage {
    src?: string;
    srcset?: string;
    full_src?: string;
    full_src_w?: number;
    full_src_h?: number;
    thumb_src?: string;
}
