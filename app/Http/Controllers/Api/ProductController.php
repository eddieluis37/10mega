<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\JsonResponse;


class ProductController extends Controller
{
    /**
     * Devuelve productos con category_id = 1
     */
    public function index(): JsonResponse
    {
        $products = Product::where('category_id', 1)
            ->get(['name', 'code', 'unitofmeasure_id', 'price_insti']);

        return response()->json([
            'data' => $products,
        ], 200);
    }
}
