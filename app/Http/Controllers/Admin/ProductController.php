<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Permission;
use App\Models\IntakePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
class ProductController extends Controller
{
    public function create(Request $request)
    {
        return view('admin-panel.product.create');
    }

    public function index(Request $request)
    {

        return view('admin-panel.product.index');
    }

    public function store(Request $request)
    { // Validation rules
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name', // Adjusted for the correct table 'products'
            'cgst' => 'nullable|numeric|min:0|max:100',
            'sgst' => 'nullable|numeric|min:0|max:100',
            'igst' => 'nullable|numeric|min:0|max:100',
            'utgst' => 'nullable|numeric|min:0|max:100',
            'price' => 'nullable|regex:/^\d+(\.\d{1,2})?$/|numeric|min:0',
        ]);

        // Create a new Product instance
        $product = new Product;
        $product->name = $request->name;
        $product->created_by = Auth::User()->id;
        $product->company_id = Auth::User()->company_id;
        $product->is_active = $request->status ?? 0;
        $product->cgst = $request->cgst;
        $product->sgst = $request->sgst;
        $product->igst = $request->igst;
        $product->utgst = $request->utgst;
        $product->price = $request->price;
        $product->product_unit = $request->unit;

        // Save the product and handle success or failure
        if ($product->save()) {
            return redirect()->route('product.index')->with('success', 'Product created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create Product. Please try again.');
        }
    }


    public function fetch(Request $request)
    {
        $query = Product::Query();

        $limit = @$request->$limit ?? 10;

        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 10;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('Products.name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('filter_btn') && !empty($request->filter_btn)) {
            if ($request->filter_btn == "total") {
            } elseif ($request->filter_btn == "active") {
                $query->where('is_active', 1);
            } elseif ($request->filter_btn == "deactive") {
                $query->where('is_active', 0);
            }
        }

        $totalPage = $query->count();
        $totalPage = ceil($totalPage / $limit);

        $products = $query->orderBy('products.id', 'desc')
        ->limit($limit)
        ->offset($offset)

        ->get()
        ->toArray();

        return response()->json([
            'html' => view('admin-panel.product.table.detail', ['products' => $products])->render(),
            'pagination' => view('admin-panel.product.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function CountData(Request $request)
    {

        $query = Product::query();


        $filters = [
            ['name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . Product::count()],
            ['name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . Product::where('is_active', 1)->count()],
            ['name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . Product::where('is_active', 0)->count()]
        ];


        // Render the HTML view with filtered counts
        $html = view('admin-panel.product.table.countData', compact( 'filters'))->render();

        return response()->json(['html' => $html]);
    }

    public function deleteIntake($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json(['message' => 'Product deleted successfully']);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }


    public function change_status(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer|exists:Products,id',
        ]);

        // Find the intake and toggle its status
        $product = Product::find($request->id);
        $product->is_active = !$product->is_active;

        // Save the updated intake and respond accordingly
        if ($product->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Product status updated successfully.',
                'status' => $product->is_active // Return the updated status
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => $product->is_active, // Return the current status, which is the same as before the save attempt
                'message' => 'Failed to update intake status.'
            ], 500);
        }
    }

    public function edit(Request $request,$id){
        $product = Product::where('id',$id)->first();

        return view('admin-panel.product.edit',compact('product'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->ignore($id),
            ],
            'cgst' => 'nullable|numeric|min:0|max:100',
            'sgst' => 'nullable|numeric|min:0|max:100',
            'igst' => 'nullable|numeric|min:0|max:100',
            'utgst' => 'nullable|numeric|min:0|max:100',
            'price' => 'nullable|regex:/^\d+(\.\d{1,2})?$/|numeric|min:0',
            'unit' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        // Find the existing product
        $product = Product::find($id);

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        // Update the product attributes
        $product->name = $request->name;
        $product->cgst = $request->cgst;
        $product->sgst = $request->sgst;
        $product->igst = $request->igst;
        $product->utgst = $request->utgst;
        $product->price = $request->price;
        $product->product_unit = $request->unit;
        $product->is_active = $request->status ?? 0;
        // $product->updated_by = Auth::user()->id; // Assuming you have a field for tracking updates

        // Save the updated product
        if ($product->save()) {
            return redirect()->route('product.index')->with('success', 'Product updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update Product. Please try again.');
        }
    }

}

