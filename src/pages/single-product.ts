/**
 * Single Product Page - Entry Point
 * 
 * Carregado condicionalmente apenas na página de produto único.
 */

// Page CSS (importa todos os componentes)
import './single-product.css';

// Component
import { initQuantityButtons } from '../components/add-to-cart/quantity';

// Init
function init(): void {
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

