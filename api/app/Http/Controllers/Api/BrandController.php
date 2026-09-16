<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Support\Facades\Cache;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Cache::remember('public.brands', 300, function () {
            return Brand::where('is_active', true)->orderBy('name')->get();
        });

        return BrandResource::collection($brands);
    }
}
