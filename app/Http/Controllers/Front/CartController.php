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
use session;
use Razorpay\Api\Api;
use Illuminate\Support\Str; 

class CartController extends Controller
{
    
    public function add(Request $request)
    {
        $productId = $request->product_id;
        $quantity = (int) $request->quant_num;

        $product = Product::findOrFail($productId);
        $cart= Cart::where('product_id',$productId)
                          ->where('user_id', auth()->id())
                          ->first();
        
        //$cart = session()->get('cart', []);
        if ($cart) {
            $cart->quantity += $quantity;
            $cart->save();
        } else {
            Cart::create([
                'user_id'     => auth()->id(),
                'product_id'  => $productId,
                'quantity'    => $quantity,
            ]);
        }

        //session()->put('cart', $cart);

        $cartItems = Cart::with('product')
                ->where('user_id', auth()->id())
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->product->name,
                        'qty' => $item->quantity,
                        'total' => $item->product->price * $item->quantity,
                    ];
                });

        return response()->json([
            'status' => 'success',
            'message' => 'Added to cart!',
            'cart_items' => $cartItems,
            'cart_count' => Cart::where('user_id', auth()->id())->count(),
        ]);
    }

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to view your cart.');
        }

        $userId = auth()->id();

        // Get all cart items for the user
        $cartItems = Cart::with('product')
            ->where('user_id', $userId)
            ->get()
            ->map(function ($item) {
                $item->total = $item->product->price * $item->quantity;
                return $item;
            });

        $totalAmount = $cartItems->sum('total');
        $cartCount = $cartItems->count();

        return view('frontend.cart.index', compact('cartItems', 'totalAmount', 'cartCount'));
    }


    public function update(Request $request) {
        Cart::where('id', $request->id)->update(['quantity' => $request->quantity]);
        return response()->json(['status' => 'success', 'message' => 'Cart updated']);
    }

    public function remove($id) {
        Cart::where('id', $id)->delete();
        return response()->json(['status' => 'success', 'message' => 'Item is removed from cart']);
    }

    public function checkout() {
        $cart = Cart::with('product')->where('user_id', Auth::id())->get();
        $subtotal = $cart->sum(function ($item) {
            return $item->product->price * $item->quantity; });
        $deliveryFee = 30;
        $total = $subtotal + $deliveryFee;
        return view('frontend.cart.checkout', compact('cart','subtotal', 'deliveryFee', 'total'));
    }

    public function createRazorpayOrder(Request $request)
    {
        $amountInPaise = max(100000, (int)($request->totalAmt * 100));

        $api = new \Razorpay\Api\Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $order = $api->order->create([
            'receipt' => Str::random(10),
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'payment_capture' => 1,
        ]);

    
        return response()->json([
            'order_id' => $order['id'],
            'amount' => $order['amount']
        ]);
    }


    public function placeOrder(Request $request)
    {
        $request->validate([
            'address' => 'required',
            'payment_method' => 'required',
        ]);

        $order = new Order();
        $order->user_id = auth()->id();
        $order->total_price = $request->total;
        $order->status = 'pending';
        $order->delivery_status = 'pending';
        $order->payment_method = $request->payment_method;
        $order->tracking_number = 'pending';
        $order->estimated_delivery = 'pending';
        $order->save();

        // Save order items logic here...

        return response()->json(['message' => 'Order placed successfully!']);
    }

}
