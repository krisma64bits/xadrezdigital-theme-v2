# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Idioma / Language

**SEMPRE responder em português brasileiro (pt-BR).**

## Visão Geral do Projeto

Este é um tema WordPress customizado para e-commerce de produtos digitais de xadrez, totalmente integrado com WooCommerce. O tema utiliza Vite + Tailwind CSS v4 para desenvolvimento moderno de assets.

## Comandos Essenciais

### Desenvolvimento
```bash
npm run dev          # Inicia servidor de desenvolvimento Vite (porta 5173)
npm run build        # Compila TypeScript e builda assets de produção
npm run preview      # Visualiza build de produção
```

### Build
- O build gera arquivos em `dist/` com manifest.json para WordPress
- TypeScript é compilado antes do build do Vite
- Assets devem ser enfileirados via `functions.php`

## Arquitetura do Projeto

### Stack Tecnológico
- **WordPress Theme** - Tema customizado compatível com WordPress + WooCommerce
- **Vite 7.2+** - Build tool e dev server
- **Tailwind CSS v4** - Framework CSS (modo strict)
- **TypeScript 5.9+** - Linguagem principal

### Estrutura de Arquivos
```
/
├── src/
│   ├── main.ts           # Entry point do Vite
│   └── style.css         # Importa Tailwind CSS
├── dist/                 # Assets compilados (gerado)
├── functions.php         # WordPress theme functions
├── header.php            # Template header
├── footer.php            # Template footer
├── index.php             # Template principal
├── style.css             # WordPress theme stylesheet (requerido)
├── vite.config.ts        # Configuração do Vite
├── system-design.json    # Design System (CRÍTICO - ler antes de qualquer mudança)
└── package.json
```

### Fluxo de Desenvolvimento

1. **Vite Dev Server** (porta 5173)
   - HMR (Hot Module Replacement) ativo
   - CORS habilitado para integração com WordPress
   - Serve assets diretamente durante desenvolvimento

2. **Build de Produção**
   - Gera manifest.json para WordPress
   - Output em `dist/`
   - Assets com hash para cache busting

3. **Integração WordPress**
   - Assets enfileirados via `wp_enqueue_scripts`
   - Templates PHP usam funções WordPress padrão
   - Compatibilidade total com WooCommerce

## System Design - REGRAS CRÍTICAS

**LEIA `system-design.json` ANTES de fazer qualquer alteração de UI/CSS.**

### Princípios Fundamentais

1. **STRICT MODE - SEM VALORES ARBITRÁRIOS**
   - ❌ PROIBIDO: `w-[500px]`, `text-[24px]`, `p-[15px]`, `bg-[#fff]`
   - ✅ PERMITIDO: Apenas classes padrão do Tailwind v4
   - System design é baseado em shadcn/ui

2. **Reutilização de Componentes**
   - SEMPRE reutilizar componentes existentes no `system-design.json`
   - Não criar componentes customizados sem permissão explícita
   - Componentes disponíveis: button, card, input, textarea, badge, alert, dialog, table, etc.

3. **Paleta de Cores**
   - Cores neutras: `slate-50` a `slate-950`
   - Cores semânticas: `red-*` (destructive), `blue-*`, `green-*`, `yellow-*`
   - Primary: `bg-slate-900 text-slate-50`
   - Secondary: `bg-slate-100 text-slate-900`

4. **Espaçamento Recomendado**
   - xs: `1` (4px)
   - sm: `2` (8px)
   - base: `4` (16px)
   - md: `6` (24px)
   - lg: `8` (32px)
   - xl: `12` (48px)
   - 2xl: `16` (64px)

5. **Tailwind Preflight (CSS Reset)**
   - Ativo por padrão
   - Remove margens/paddings de elementos
   - Headings (h1-h6) DEVEM ter classes de tamanho: `text-4xl`, `text-3xl`, etc.
   - Listas DEVEM ter `list-disc` ou `list-decimal` + `pl-5`
   - Espaçamento DEVE ser adicionado manualmente: `space-y-4`, `gap-6`, `mt-4`
   - Focus states DEVEM ser adicionados: `focus-visible:ring-2`

### Exemplos de Uso Correto

#### Button
```html
<!-- Default -->
<button class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-slate-900 text-slate-50 hover:bg-slate-800 h-10 px-4 py-2">
  Clique aqui
</button>

<!-- Outline -->
<button class="... border border-slate-300 bg-white hover:bg-slate-100 hover:text-slate-900 ...">
  Outline
</button>
```

#### Card
```html
<div class="rounded-lg border border-slate-200 bg-white shadow-sm">
  <div class="flex flex-col space-y-1.5 p-6">
    <h3 class="text-2xl font-semibold leading-tight tracking-tight">Título</h3>
    <p class="text-sm text-slate-500">Descrição</p>
  </div>
  <div class="p-6 pt-0">
    Conteúdo
  </div>
</div>
```

#### Input
```html
<input type="text" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm ring-offset-white placeholder:text-slate-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
```

## Regras de Desenvolvimento

### CSS/Tailwind
1. **NÃO customizar classes Tailwind** - usar apenas classes padrão
2. **NÃO criar CSS customizado** - exceto quando absolutamente necessário
3. **Consultar `system-design.json`** antes de qualquer mudança de estilo
4. **Mobile-first** - começar com classes base, adicionar breakpoints: `md:`, `lg:`
5. **Acessibilidade** - sempre incluir estados de focus e disabled

### WordPress/PHP
1. **Compatibilidade WooCommerce** - tema deve suportar todas funções WooCommerce
2. **WordPress Coding Standards** - seguir padrões WordPress
3. **Segurança** - sempre usar funções de escape: `esc_html()`, `esc_url()`, etc.
4. **Hooks** - usar `add_action()` e `add_filter()` apropriadamente
5. **Enfileiramento de assets** - usar `wp_enqueue_style()` e `wp_enqueue_script()`

### TypeScript
1. **Type Safety** - sempre tipar corretamente
2. **Build antes de commitar** - garantir que `npm run build` funciona
3. **Entry point** - todo código TS deve ser importado via `src/main.ts`

## WooCommerce

Este tema é focado em **e-commerce de produtos digitais de xadrez**. Considerações:

1. **Templates WooCommerce** - criar em `woocommerce/` quando necessário
2. **Hooks WooCommerce** - usar para customizações
3. **Product Types** - focar em produtos digitais (downloads)
4. **Checkout** - otimizar para produtos digitais
5. **Emails** - customizar templates de email se necessário

## Configuração Vite

- **Dev Server**: porta 5173, CORS habilitado, host exposto
- **Build**: manifest.json gerado, output em `dist/`
- **Entry**: `src/main.ts`
- **Tailwind**: plugin `@tailwindcss/vite` integrado

## Workflow de Mudanças

1. Ler `system-design.json` para entender restrições
2. Reutilizar componentes existentes
3. Seguir padrões Tailwind v4 (sem valores arbitrários)
4. Testar em ambiente WordPress + WooCommerce
5. Verificar build de produção (`npm run build`)
6. Garantir compatibilidade mobile (responsive)

## Proibições

❌ **NÃO fazer sem permissão explícita:**
- Customizar classes Tailwind com valores arbitrários
- Criar CSS customizado
- Fugir das especificações do `system-design.json`
- Modificar componentes sem consultar design system
- Usar inline styles (`style="..."`)
- Usar `!important`

## Foco em E-commerce de Xadrez

O site vende **produtos digitais de xadrez**. Considerar:
- Cursos de xadrez
- E-books e PDFs
- Vídeo-aulas
- Ferramentas e softwares
- Assinaturas/memberships
- Downloads digitais

Templates e componentes devem refletir este contexto.
