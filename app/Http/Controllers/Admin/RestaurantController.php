<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Restaurant;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Restaurant::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('image', function($row) {
                    $url = asset('storage/' . $row->image);
                        if (!file_exists(public_path('storage/' . $row->image))) {
                            $url = asset('images/default.png'); // fallback image
                        }
                    return '<img src="' . $url . '" width="80" height="80" class="img-thumbnail" />';
                })
                ->addColumn('action', function ($row) {
                    return '
                    <button class="btn btn-sm btn-info editBtn" data-id="'.$row->id.'">Edit</button>
                    <button class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">Delete</button>';
                })
                ->rawColumns(['image','action'])
                ->make(true);
        }
        return view('admin.restaurants.index');
    }

    public function show(Restaurant $restaurant)
    {
        return response()->json($restaurant);
    }

    public function store(StoreRestaurantRequest $request)
    {
        $data = $request->validated();
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('restaurants', 'public'); 
                $data['image'] = $path;
            }
        Restaurant::create($data);
        return response()->json(['status' => 'success', 'message' => 'Restaurant created successfully']);
    }

    public function edit($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        return response()->json($restaurant);
    }

    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant)
    {
        $data = $request->validated();
            if ($request->hasFile('image')) {
                 if ($restaurant->image && \Storage::disk('public')->exists($restaurant->image)) {
                        \Storage::disk('public')->delete($restaurant->image);
                    }
                $path = $request->file('image')->store('restaurants', 'public');
                $data['image'] = $path;
            }
        $restaurant->update($data);
        return response()->json(['status' => 'success', 'message' => 'Restaurant updated successfully']);
    }

    public function destroy($id)
    {
        Restaurant::findOrFail($id)->delete();
        return response()->json(['status' => 'success', 'message' => 'Restaurant deleted']);
    }   

}
