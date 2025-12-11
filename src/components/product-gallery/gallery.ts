/**
 * Product Gallery - Main Logic
 */

import Swiper from 'swiper';
import { Navigation, Thumbs, FreeMode } from 'swiper/modules';
import type { ProductGalleryConfig } from './types.ts';
import { DEFAULT_CONFIG } from './types.ts';
import { setupVariationSupport } from './variations.ts';

/**
 * Inicializa a galeria de produto
 */
export function initProductGallery(config: Partial<ProductGalleryConfig> = {}): void {
    const settings = { ...DEFAULT_CONFIG, ...config };

    const mainElement = document.querySelector<HTMLElement>(settings.mainSelector);
    const thumbsElement = document.querySelector<HTMLElement>(settings.thumbsSelector);

    if (!mainElement) {
        return;
    }

    // Inicializa thumbs swiper primeiro (se existir)
    let thumbsSwiper: Swiper | null = null;

    if (thumbsElement) {
        thumbsSwiper = new Swiper(thumbsElement, {
            modules: [FreeMode],
            spaceBetween: 8,
            slidesPerView: 'auto',
            freeMode: true,
            watchSlidesProgress: true,
            direction: 'horizontal',
            breakpoints: {
                1024: {
                    direction: 'vertical',
                    spaceBetween: 8,
                },
            },
            on: {
                init: (swiper) => {
                    updateActiveThumb(swiper, 0, settings.activeThumbClass);
                },
            },
        });
    }

    // Inicializa main swiper
    const mainSwiper = new Swiper(mainElement, {
        modules: [Navigation, Thumbs],
        spaceBetween: 0,
        slidesPerView: 1,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        thumbs: thumbsSwiper ? { swiper: thumbsSwiper } : undefined,
        on: {
            slideChange: (swiper) => {
                if (thumbsSwiper) {
                    updateActiveThumb(thumbsSwiper, swiper.activeIndex, settings.activeThumbClass);
                }
            },
        },
    });

    // Suporte a variações de produto WooCommerce
    setupVariationSupport(mainSwiper, thumbsSwiper);
}

/**
 * Atualiza a classe ativa no thumbnail
 */
function updateActiveThumb(thumbsSwiper: Swiper, activeIndex: number, activeClass: string): void {
    const slides = thumbsSwiper.slides;
    const classes = activeClass.split(' ');

    slides.forEach((slide, index) => {
        if (index === activeIndex) {
            slide.classList.add(...classes);
        } else {
            slide.classList.remove(...classes);
        }
    });
}
