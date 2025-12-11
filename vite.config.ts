import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        tailwindcss(),
    ],

    // Configuração para build de produção
    build: {
        // Gera o manifest.json para o WordPress ler os assets
        manifest: true,

        // Diretório de saída
        outDir: 'dist',

        // Limpa o diretório de saída antes de cada build
        emptyOutDir: true,

        // Ponto de entrada
        rollupOptions: {
            input: {
                main: 'src/main.ts',
                'single-product': 'src/pages/single-product.ts',
            },
            output: {
                // Coloca todos os chunks no mesmo bundle para evitar problemas de path
                manualChunks: undefined,
            },
        },
    },

    // Base path relativo - será resolvido em runtime
    base: './',

    // Configuração do servidor de desenvolvimento
    server: {
        // Permite CORS para o WordPress acessar
        cors: true,

        // Porta padrão
        port: 5173,

        // Expõe para a rede (útil se WordPress estiver em outro host)
        host: true,
    },
})
