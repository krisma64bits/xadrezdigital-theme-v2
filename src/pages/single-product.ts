/**
 * Single Product Page - Entry Point
 * 
 * Carregado condicionalmente apenas na página de produto único.
 */

// Swiper CSS
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/thumbs';
import 'swiper/css/free-mode';

// Component CSS
import '../components/product-gallery/styles.css';

// Component
import { initProductGallery } from '../components/product-gallery';

// Init
function init(): void {
    if (document.querySelector('.xd-product-gallery')) {
        initProductGallery();
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

