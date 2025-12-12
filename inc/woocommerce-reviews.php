<?php
/**
 * WooCommerce Reviews Customization
 * Summary de avaliações com design shadcn-like + slide-down form
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Renderiza o summary de avaliações com distribuição por estrelas
 */
function xadrez_render_reviews_summary() {
    global $product;
    
    if (!$product || !comments_open()) {
        return;
    }
    
    $avg_rating = (float) $product->get_average_rating();
    $review_count = (int) $product->get_review_count();
    
    // Calcula distribuição por estrelas
    $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
    
    if ($review_count > 0) {
        $reviews = get_comments([
            'post_id' => $product->get_id(),
            'status' => 'approve',
            'type' => 'review'
        ]);
        
        foreach ($reviews as $review) {
            $rating = (int) get_comment_meta($review->comment_ID, 'rating', true);
            if ($rating >= 1 && $rating <= 5) {
                $distribution[$rating]++;
            }
        }
    }
    
    // Total de avaliações para cálculo proporcional
    $total_reviews = array_sum($distribution) ?: 1;
    ?>
    
    <div class="xd-reviews-summary">
        <!-- Header com título e resumo -->
        <div class="xd-reviews-summary__header">
            <h2 class="xd-reviews-summary__title">Avaliações</h2>
            <?php if ($review_count > 0): ?>
                <div class="xd-reviews-summary__meta">
                    <div class="xd-reviews-summary__stars">
                        <?php echo wc_get_rating_html($avg_rating, $review_count); ?>
                    </div>
                    <span class="xd-reviews-summary__rating">(<?php echo number_format($avg_rating, 1, ',', '.'); ?>)</span>
                    <span class="xd-reviews-summary__count"><?php echo $review_count; ?> <?php echo $review_count === 1 ? 'avaliação' : 'avaliações'; ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Corpo do summary -->
        <div class="xd-reviews-summary__body">
            <!-- Coluna esquerda: Nota média e botão -->
            <div class="xd-reviews-summary__left">
                <?php if ($review_count > 0): ?>
                    <div class="xd-reviews-summary__avg">
                        <span class="xd-reviews-summary__avg-value"><?php echo number_format($avg_rating, 1, ',', '.'); ?></span>
                        <span class="xd-reviews-summary__avg-max">de 5</span>
                    </div>
                <?php else: ?>
                    <p class="xd-reviews-summary__empty">Nenhuma avaliação ainda.</p>
                <?php endif; ?>
                
                <button 
                    type="button" 
                    class="xd-reviews-summary__btn"
                    id="xd-toggle-review-form"
                >
                    Escrever avaliação
                </button>
            </div>
            
            <!-- Coluna direita: Distribuição por estrelas -->
            <?php if ($review_count > 0): ?>
                <div class="xd-reviews-summary__distribution">
                <?php for ($i = 5; $i >= 1; $i--): 
                        $count = $distribution[$i];
                        $percentage = ($count / $total_reviews) * 100;
                    ?>
                        <div class="xd-reviews-summary__bar-row">
                            <span class="xd-reviews-summary__bar-label"><?php echo $i; ?></span>
                            <span class="xd-reviews-summary__bar-star">★</span>
                            <div class="xd-reviews-summary__bar-track">
                                <div class="xd-reviews-summary__bar-fill" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                            <span class="xd-reviews-summary__bar-count"><?php echo $count; ?></span>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('xd-toggle-review-form');
        const formWrapper = document.getElementById('review_form_wrapper');
        
        if (btn && formWrapper) {
            // Toggle slide-down
            btn.addEventListener('click', function() {
                formWrapper.classList.toggle('xd-form-open');
                
                // Atualiza texto do botão
                if (formWrapper.classList.contains('xd-form-open')) {
                    btn.textContent = 'Cancelar';
                } else {
                    btn.textContent = 'Escrever avaliação';
                }
            });
        }
    });
    </script>
    <?php
}

/**
 * Remove o título padrão das reviews
 */
add_filter('woocommerce_reviews_title', '__return_empty_string');

/**
 * Injeta o summary no TOPO da seção de reviews via output buffering
 */
add_action('woocommerce_before_single_product', function() {
    ob_start();
}, 5);

add_action('woocommerce_after_single_product', function() {
    $html = ob_get_clean();
    
    // Encontra a seção de reviews e injeta o summary no topo
    $html = preg_replace(
        '/(<div id="reviews" class="[^"]*">)/i',
        '$1' . xadrez_get_reviews_summary_html(),
        $html
    );
    
    echo $html;
}, 5);

/**
 * Retorna o HTML do summary (para usar no preg_replace)
 */
function xadrez_get_reviews_summary_html() {
    global $product;
    
    if (!$product || !comments_open()) {
        return '';
    }
    
    ob_start();
    xadrez_render_reviews_summary();
    return ob_get_clean();
}
