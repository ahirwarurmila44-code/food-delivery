<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Product;
use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // public function add(Request $request)
    // {
    //     $product = Product::findOrFail($request->product_id);
    //     $cart = session()->get('cart', []);

    //     if (isset($cart[$product->id])) {
    //         $cart[$product->id]['quantity'] += 1;
    //     } else {
    //         $cart[$product->id] = [
    //             "name" => $product->name,
    //             "price" => $product->price,
    //             "quantity" => 1,
    //         ];
    //     }

    //     session()->put('cart', $cart);

    //     return response()->json(['success' => 'Product added to cart']);
    // }

    // public function add(Request $request)
    // {
    //     $request->validate([
    //         'product_id' => 'required|exists:products,id',
    //     ]);
    //     $productId = $request->input('product_id');

    //     $cart = session()->get('cart', []);
    //     $cart[$productId] = ($cart[$productId] ?? 0) + 1;
    //     session()->put('cart', $cart);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Added to cart!',
    //         'cart_count' => count($cart),
    //         'cart_items' => $cart
    //     ]);
    // }

    public function add(Request $request)
    {
        $productId = $request->product_id;
        $quantity = $request->quant_num;
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please log in to add to cart.'
            ], 401);
        }

        $cartItem = Cart::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->first();
        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        // Prepare cart item data to return
        $items = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'qty' => $item->quantity,
                    'total' => $item->quantity * $item->product->price
                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => 'Added to cart!',
            'cart_items' => $items,
            'cart_count' => $items->count()
        ]);
    }
    
    public function index()
    {
        $cartCount = auth()->check()
            ? Cart::where('user_id', auth()->id())->count()
            : 0;

        return view('frontend.cart.index', compact('cartCount'));
    }

    public function update(Request $request) {
        Cart::where('id', $request->id)->update(['quantity' => $request->quantity]);
        return response()->json(['status' => 'success', 'message' => 'Cart updated']);
    }

    public function remove($id) {
        Cart::where('id', $id)->delete();
        return response()->json(['status' => 'success', 'message' => 'Removed from cart']);
    }

    public function checkout() {
        $cart = Cart::with('product')->where('user_id', Auth::id())->get();
        return view('frontend.cart.checkout', compact('cart'));
    }

    public function placeOrder(Request $request) {
        $order = Order::create([
            'user_id' => Auth::id(),
            'total' => $request->total,
            'address' => $request->address,
            'payment_method' => $request->payment_method,
            'status' => 'Pending',
        ]);

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        Cart::where('user_id', Auth::id())->delete();

        return response()->json(['status' => 'success', 'message' => 'Order placed successfully!']);
    }

    // public function checkout()
    // {
    //     $cart = Cart::where('user_id', auth()->id())->with('product')->get();
    //     return view('checkout', compact('cart'));
    // }
    public function showCheckout()
    {
        $cartItems = session('cart', []);
        $subtotal = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        $deliveryFee = 30;
        $total = $subtotal + $deliveryFee;

        return view('frontend.checkout', compact('cartItems', 'subtotal', 'deliveryFee', 'total'));
    }
}
