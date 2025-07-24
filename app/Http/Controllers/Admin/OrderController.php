<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\{Order,OrderItem,Product,Customer};
use Yajra\DataTables\Facades\DataTables;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendInvoiceJob;
use DB;
use PDF;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with('customer')->latest()->get();
            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('customer', fn($order) => $order->customer->name)
                ->addColumn('status', fn($order) => ucfirst($order->status))
                ->addColumn('delivery', fn($order) => ucfirst($order->delivery_status))
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-info view-btn" data-id="'.$row->id.'">View</button>
                            <button class="btn btn-sm btn-primary edit-btn" data-id="'.$row->id.'">Edit</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'">Delete</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $customers = Customer::all();
        $products = Product::where('available', 1)->get();
        return view('admin.orders.index', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_ids' => 'required|array',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            $total = 0;
            foreach ($request->product_ids as $i => $pid) {
                $product = Product::findOrFail($pid);
                $qty = $request->quantities[$i];
                $total += $product->price * $qty;
            }

            $order = Order::create([
                'customer_id' => $request->customer_id,
                'total_price' => $total,
                'status' => 'pending',
            ]);

            foreach ($request->product_ids as $i => $pid) {
                $product = Product::find($pid);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $pid,
                    'quantity' => $request->quantities[$i],
                    'price' => $product->price,
                ]);
            }

            DB::commit();
             try {
        SendInvoiceJob::dispatch($order);
    } catch (\Throwable $e) {
        \Log::error('Invoice job failed: ' . $e->getMessage());
    }
            return response()->json(['success' => 'Order placed & invoice emailed successfully']);
            //return response()->json(['success' => 'Order created successfully']);
        } catch (\Exception $e) {
    DB::rollBack();
    \Log::error('Order store error: ' . $e->getMessage());
    return response()->json(['error' => $e->getMessage()], 500); // show error for debugging
}
    }

    public function show(Order $order)
    {
        $order->load('items.product');
        return response()->json($order);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['success' => 'Order deleted successfully']);
    }
    public function view(Order $order)
    {
        $order->load('customer', 'items.product');
        return response()->json($order);
    }

    public function invoice(Order $order)
    {
        $order->load('customer', 'items.product');
        $pdf = PDF::loadView('admin.orders.invoice', compact('order'));
        return $pdf->download('invoice_order_'.$order->id.'.pdf');
    }

    public function sendInvoice(Order $order)
    {
        Mail::to($order->customer->email)->send(new InvoiceMail($order));
        return response()->json(['success' => 'Invoice sent to email successfully']);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
        'status' => 'required|in:pending,completed,cancelled',
        'delivery_status' => 'nullable|in:pending,shipped,in_transit,delivered,cancelled',
        'tracking_number' => 'nullable|string|max:255',
        'estimated_delivery' => 'nullable|date',
    ]);

    $order->update([
        'status' => $request->status,
        'delivery_status' => $request->delivery_status,
        'tracking_number' => $request->tracking_number,
        'estimated_delivery' => $request->estimated_delivery,
    ]);

    return response()->json(['success' => 'Order updated with tracking info']);
    }
}