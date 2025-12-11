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

// Page CSS (importa todos os componentes)
import './single-product.css';

// Component
import { initProductGallery } from '../components/product-gallery';
import { initQuantityButtons } from '../components/add-to-cart/quantity';

// Init
function init(): void {
    if (document.querySelector('.xd-product-gallery')) {
        initProductGallery();
    }

    // Inicializa botões +/- no seletor de quantidade
    if (document.querySelector('.quantity')) {
        initQuantityButtons();
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

