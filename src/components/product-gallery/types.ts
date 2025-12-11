/**
 * Product Gallery - Types
 */

export interface ProductGalleryConfig {
    mainSelector: string;
    thumbsSelector: string;
    activeThumbClass: string;
}

export const DEFAULT_CONFIG: ProductGalleryConfig = {
    mainSelector: '#xd-main-swiper',
    thumbsSelector: '#xd-thumbs-swiper',
    activeThumbClass: '!border-amber-500',
};
