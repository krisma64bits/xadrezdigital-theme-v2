/**
 * Add to Cart - Quantity Selector Enhancement
 * 
 * Adiciona botões +/- ao seletor de quantidade do WooCommerce
 * mantendo 100% de compatibilidade com o markup original.
 */

export function initQuantityButtons(): void {
    const quantityContainers = document.querySelectorAll<HTMLElement>('.quantity');

    quantityContainers.forEach((container) => {
        // Evita duplicação
        if (container.querySelector('.xd-qty-btn')) return;

        const input = container.querySelector<HTMLInputElement>('.qty');
        if (!input) return;

        const min = parseFloat(input.getAttribute('min') || '1');
        const max = parseFloat(input.getAttribute('max') || '999');
        const step = parseFloat(input.getAttribute('step') || '1');

        // Criar botão de diminuir
        const minusBtn = document.createElement('button');
        minusBtn.type = 'button';
        minusBtn.className = 'xd-qty-btn xd-qty-minus';
        minusBtn.innerHTML = '−';
        minusBtn.setAttribute('aria-label', 'Diminuir quantidade');

        // Criar botão de aumentar
        const plusBtn = document.createElement('button');
        plusBtn.type = 'button';
        plusBtn.className = 'xd-qty-btn xd-qty-plus';
        plusBtn.innerHTML = '+';
        plusBtn.setAttribute('aria-label', 'Aumentar quantidade');

        // Inserir botões
        container.insertBefore(minusBtn, input);
        container.appendChild(plusBtn);

        // Event handlers
        minusBtn.addEventListener('click', () => {
            const currentVal = parseFloat(input.value) || min;
            const newVal = Math.max(min, currentVal - step);
            input.value = String(newVal);
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });

        plusBtn.addEventListener('click', () => {
            const currentVal = parseFloat(input.value) || min;
            const newVal = Math.min(max, currentVal + step);
            input.value = String(newVal);
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
}
