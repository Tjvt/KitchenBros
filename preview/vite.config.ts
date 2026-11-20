import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

const base = process.env.PAGES_BASE || '/KitchenBros/';
export default defineConfig({
    root: fileURLToPath(new URL('.', import.meta.url)),
    base,
    publicDir: false,
    plugins: [
        {
            name: 'preview-model-base',
            transform(code, id) {
                if (id.split('?')[0].endsWith('/landing/FlavorReactor.vue')) {
                    return code.replace("'/3D/steak-plate.glb'", JSON.stringify(base + '3D/steak-plate.glb'));
                }
            },
        },
        vue(),
        tailwindcss(),
    ],
    build: {
        outDir: '../dist-preview',
        emptyOutDir: true,
    },
});
