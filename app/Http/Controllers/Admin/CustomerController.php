<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Customer;
use App\Http\Requests\CustomerRequest;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-primary edit-btn" data-id="'.$row->id.'">Edit</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'">Delete</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.customers.index');
    }

    public function store(CustomerRequest $request)
    {
        Customer::create($request->validated());
        return response()->json(['success' => 'Customer created']);
    }

    public function show(Customer $customer)
    {
        return response()->json($customer);
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());
        return response()->json(['success' => 'Customer updated']);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['success' => 'Customer deleted']);
    }

}