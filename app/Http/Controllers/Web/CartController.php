<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display the cart
     */
    public function show()
    {
        $cart = $this->getOrCreateCart();
        $cart->load(['items.product.category']);

        // Ensure price is properly cast as float
        $cart->items->each(function ($item) {
            $item->price = floatval($item->price);
        });

        // Calculate totals manually to ensure they're available
        $cart->total = $cart->items->sum(function ($item) {
            return floatval($item->price) * $item->quantity;
        });
        
        $cart->total_items = $cart->items->sum('quantity');

        return Inertia::render('Web/Cart/Show', [
            'cart' => $cart,
        ]);
    }

    /**
     * Add a product to the cart
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_uuid' => 'required|exists:products,uuid',
            'quantity' => 'integer|min:1|max:10',
        ]);

        $product = Product::where('uuid', $validated['product_uuid'])->firstOrFail();
        
        if (!$product->is_available) {
            return redirect()->back()->with('error', 'This product is currently unavailable.');
        }

        $cart = $this->getOrCreateCart();
        $quantity = $validated['quantity'] ?? 1;

        // Check if item already exists in cart
        $existingItem = $cart->items()->where('product_id', $product->id)->first();

        if ($existingItem) {
            // Update quantity if item exists
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity
            ]);
        } else {
            // Add new item to cart
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
        }

        return redirect()->route('web.cart.show')
            ->with('success', $product->name . ' added to cart!');
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cartItem->update([
            'quantity' => $validated['quantity']
        ]);

        return redirect()->route('web.cart.show')
            ->with('success', 'Cart updated successfully!');
    }

    /**
     * Remove item from cart
     */
    public function remove(CartItem $cartItem)
    {
        $productName = $cartItem->product->name;
        $cartItem->delete();

        return redirect()->route('web.cart.show')
            ->with('success', $productName . ' removed from cart!');
    }

    /**
     * Clear all items from cart
     */
    public function clear()
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->delete();

        return redirect()->route('web.cart.show')
            ->with('success', 'Cart cleared successfully!');
    }

    /**
     * Get or create cart for current user/session
     */
    private function getOrCreateCart()
    {
        if (Auth::check()) {
            // For authenticated users
            $cart = Cart::where('user_id', Auth::id())->first();
            if (!$cart) {
                $cart = Cart::create([
                    'user_id' => Auth::id(),
                ]);
            }
        } else {
            // For guest users (using session)
            $sessionId = session()->getId();
            $cart = Cart::where('session_id', $sessionId)->first();
            if (!$cart) {
                $cart = Cart::create([
                    'session_id' => $sessionId,
                ]);
            }
        }

        return $cart;
    }
}