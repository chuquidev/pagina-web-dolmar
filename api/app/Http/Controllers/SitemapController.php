<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        $xml = Cache::remember('sitemap.xml', now()->addHours(6), function () {
            $baseUrl = rtrim(config('app.frontend_url'), '/');
            $today = now()->toAtomString();

            // /catalogo lleva noindex (solo la home debe aparecer en resultados),
            // así que no va en el sitemap: listar ahí una URL que le decimos a
            // Google que no indexe es una señal contradictoria. Las fichas de
            // producto sí quedan, esas son las que queremos que aparezcan.
            $urls = collect([
                ['loc' => $baseUrl, 'priority' => '1.0', 'changefreq' => 'daily', 'lastmod' => $today],
                ['loc' => $baseUrl . '/nosotros', 'priority' => '0.5', 'changefreq' => 'monthly', 'lastmod' => $today],
                ['loc' => $baseUrl . '/preguntas-frecuentes', 'priority' => '0.4', 'changefreq' => 'monthly', 'lastmod' => $today],
                ['loc' => $baseUrl . '/guia-tallas', 'priority' => '0.4', 'changefreq' => 'monthly', 'lastmod' => $today],
                ['loc' => $baseUrl . '/reservar-mantenimiento', 'priority' => '0.6', 'changefreq' => 'monthly', 'lastmod' => $today],
            ]);

            foreach (Product::where('is_active', true)->get() as $product) {
                $urls->push([
                    'loc' => $baseUrl . '/producto/' . $product->slug,
                    'priority' => '0.8',
                    'changefreq' => 'weekly',
                    'lastmod' => $product->updated_at?->toAtomString() ?? $today,
                ]);
            }

            return view('sitemap', ['urls' => $urls->all()])->render();
        });

        return response($xml)->header('Content-Type', 'text/xml');
    }
}
