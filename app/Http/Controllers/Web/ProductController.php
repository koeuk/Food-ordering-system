<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of products for public viewing (menu).
     */
    public function index(Request $request)
    {
        $products = Product::with(['category', 'inventory'])
            ->where('is_available', true) // Only show available products
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->orderBy('name')
            ->paginate(12);

        $categories = Category::orderBy('name')->get();

        return Inertia::render('Web/Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category_id']),
        ]);
    }

    /**
     * Display the specified product for public viewing.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'inventory']);
        
        // Only show available products to public
        if (!$product->is_available) {
            abort(404);
        }

        return Inertia::render('Web/Products/Show', [
            'product' => $product,
        ]);
    }

    /**
     * Get a random product for "Add Random Product" functionality
     */
    public function random()
    {
        $randomProduct = Product::where('is_available', true)
            ->inRandomOrder()
            ->with(['category'])
            ->first();

        return response()->json($randomProduct);
    }

    /**
     * Get related products (same category, excluding current product)
     */
    public function related(Product $product)
    {
        $relatedProducts = Product::where('is_available', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category'])
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return response()->json([
            'related_products' => $relatedProducts
        ]);
    }
}
