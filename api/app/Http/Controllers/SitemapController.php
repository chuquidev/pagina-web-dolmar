<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class SitemapController extends Controller
{
    public function index()
    {
        $baseUrl = rtrim(env('FRONTEND_URL'), '/');

        $urls = collect([
            ['loc' => $baseUrl, 'priority' => '1.0'],
            ['loc' => $baseUrl . '/catalogo', 'priority' => '0.9'],
        ]);

        foreach (Category::where('is_active', true)->get() as $category) {
            $urls->push(['loc' => $baseUrl . '/catalogo?category=' . $category->slug, 'priority' => '0.7']);
        }

        foreach (Product::where('is_active', true)->get() as $product) {
            $urls->push(['loc' => $baseUrl . '/producto/' . $product->slug, 'priority' => '0.8']);
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'text/xml');
    }
}
