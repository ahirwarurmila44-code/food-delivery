<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Product;
use App\Models\Admin\Category;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::with('category','restaurant')->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('image', function($row) {
                    $url = asset('storage/' . $row->image);
                        if (!file_exists(public_path('storage/' . $row->image))) {
                            $url = asset('images/default.png'); // fallback image
                        }
                    return '<img src="' . $url . '" width="80" height="80" class="img-thumbnail" />';
                })
                ->addColumn('category', function($row) {
                    return $row->category->name ?? '-';
                })
                ->addColumn('restaurant', function($row) {
                    return $row->restaurant->name ?? '-';
                })
                ->editColumn('available', function($row) {
                    $status = $row->available ? 'Yes' : 'No';
                    $class = $row->available ? 'btn-success' : 'btn-danger';
                    return '<span class="badge rounded-pill toggle-status" 
                                style="background-color:'. ($row->available ? '#d1e7dd' : '#f8d7da') .'; 
                                        color:'. ($row->available ? '#0f5132' : '#842029') .';
                                        font-weight:500;
                                        cursor:pointer;
                                        border: 1px solid '. ($row->available ? '#badbcc' : '#f5c2c7') .';"
                                data-id="' . $row->id . '">' .
                                ($row->available ? '✔ Available' : '✘ Unavailable') . '</span>';
                })
                ->addColumn('action', function($row){
                    return '
                            <button data-id="'.$row->id.'" class="btn btn-sm btn-primary edit-btn" style="background-color:#d1e7dd; 
                              color:#0f5132; font-weight:500;  cursor:pointer; border: 1px solid #badbcc;">Edit</button>
                            <button data-id="'.$row->id.'" class="btn btn-sm btn-danger delete-btn" style="background-color:#f8d7da; 
                              color:#842029; font-weight:500;  cursor:pointer; border: 1px solid #f5c2c7;">Delete</button>';
                })
                ->rawColumns(['image','category','available','action'])
                ->make(true);
        }

        return view('admin.products.index');
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public'); 
                $data['image'] = $path;
            }
        Product::create($data);
        return response()->json(['success' => 'Product created successfully']);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
            if ($request->hasFile('image')) {
                 if ($product->image && \Storage::disk('public')->exists($product->image)) {
                        \Storage::disk('public')->delete($product->image);
                    }
                $path = $request->file('image')->store('products', 'public');
                $data['image'] = $path;
            }
        $product->update($data);
        return response()->json(['success' => 'Product updated successfully']);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['success' => 'Product deleted successfully']);
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function toggleAvailable(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->available = !$product->available;
        $product->save();

        return response()->json([
            'status' => true,
            'message' => 'Availability toggled successfully.',
        ]);
    }
}
