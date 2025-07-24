<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Admin\Category;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<button data-id="'.$row->id.'" class="btn btn-sm btn-primary edit-btn">Edit</button>
                            <button data-id="'.$row->id.'" class="btn btn-sm btn-danger delete-btn">Delete</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.categories.index');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Category::create($request->only('name'));
        return response()->json(['success' => 'Category created']);
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category->update($request->only('name'));
        return response()->json(['success' => 'Category updated']);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['success' => 'Category deleted']);
    }

}