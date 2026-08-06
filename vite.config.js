import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Huruf diambil lewat Bunny Fonts, bukan Google Fonts: berkasnya
            // ikut terunduh ke proyek saat build, sehingga halaman tidak
            // bergantung pada server pihak ketiga dan tidak mengirim alamat IP
            // pengunjung ke mana pun.
            fonts: [
                bunny('Inter', {
                    weights: [400, 500, 600, 700],
                }),
                bunny('Space Grotesk', {
                    weights: [500, 600, 700],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
