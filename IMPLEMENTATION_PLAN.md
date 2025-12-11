# Plano de Implementação - Galeria de Produto com Swiper.js

## Objetivo
Substituir a galeria padrão do WooCommerce por uma galeria customizada usando Swiper.js, com thumbnails verticais à esquerda, mantendo 100% de compatibilidade com SEO e markup.

---

## Fase 1: Estrutura Base

### 1.1 Instalar Swiper.js
```bash
npm install swiper
```

### 1.2 Criar Template da Galeria
- **Arquivo**: `woocommerce/single-product/product-gallery-swiper.php`
- **Conteúdo**:
  - Swiper principal (imagem grande)
  - Swiper thumbs (thumbnails verticais à esquerda)
  - Markup SEO-friendly com `srcset`, `alt`, `data-attributes`

### 1.3 Hooks PHP
- **Arquivo**: `inc/woocommerce-gallery.php`
- **Ações**:
  - Remover galeria padrão: `remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20)`
  - Adicionar galeria customizada: `add_action('woocommerce_before_single_product_summary', 'xd_custom_product_gallery', 20)`
  - Desabilitar PhotoSwipe nativo (opcional)

### 1.4 JavaScript do Swiper
- **Arquivo**: `resources/js/modules/product-gallery.js`
- **Funcionalidades**:
  - Inicializar Swiper principal
  - Inicializar Swiper thumbs (vertical, à esquerda)
  - Sincronizar os dois sliders

### 1.5 Estilos CSS/Tailwind
- **Arquivo**: `src/style.css` (ou arquivo dedicado)
- **Layout**:
  - Container flex com thumbs à esquerda
  - Thumbs em coluna vertical
  - Responsivo: horizontal no mobile

---

## Fase 2: Compatibilidade WooCommerce

### 2.1 Suporte a Variações de Produto
- Escutar evento `found_variation`
- Atualizar imagem principal quando variação selecionada
- Escutar evento `reset_image` para voltar ao original

### 2.2 Lightbox (Opcional)
- Integrar com PhotoSwipe ou outra biblioteca
- Manter `data-large_image` para compatibilidade

### 2.3 Zoom (Opcional)
- Integrar biblioteca de zoom ou implementar CSS zoom on hover

---

## Fase 3: SEO e Performance

### 3.1 Markup Obrigatório
- [ ] `alt` text em todas as imagens
- [ ] `srcset` e `sizes` para responsividade
- [ ] `loading="lazy"` nas imagens (exceto primeira)
- [ ] `decoding="async"`
- [ ] `data-large_image`, `data-large_image_width`, `data-large_image_height`

### 3.2 Verificar Schema.org
- Confirmar que WooCommerce ainda gera JSON-LD com imagens do produto

---

## Arquivos a Criar/Modificar

| Arquivo | Ação |
|---------|------|
| `inc/woocommerce-gallery.php` | Criar |
| `woocommerce/single-product/product-gallery-swiper.php` | Criar |
| `resources/js/modules/product-gallery.js` | Criar |
| `src/style.css` | Modificar |
| `functions.php` | Modificar (incluir novo arquivo) |
| `src/main.ts` | Modificar (importar Swiper) |

---

## Checklist Final

- [ ] Galeria funciona no desktop
- [ ] Galeria funciona no mobile
- [ ] Thumbnails à esquerda (vertical) no desktop
- [ ] Thumbnails horizontal no mobile
- [ ] Variações de produto trocam imagem
- [ ] Alt text presente em todas imagens
- [ ] Srcset funcionando
- [ ] Schema.org Product com imagens corretas
- [ ] Performance: LCP < 2.5s

---

## Status

- **Fase 1**: ✅ Concluída
- **Fase 2**: ⏳ Pendente
- **Fase 3**: ⏳ Pendente
