<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesProduct;

class SalesProductController extends Controller
{
    public function fetchSalesProducts()
    {
        $products = SalesProduct::where('tenant_id', Auth::user()->tenant_id)->paginate(5);
        return response()->json($products);
    }

    public function index()
    {
        $products = SalesProduct::where('tenant_id', Auth::user()->tenant_id)->paginate(10);
        return view('product');
    }

    public function update(Request $request, $id)
    {
        $product = SalesProduct::where('tenant_id', Auth::user()->tenant_id)
                              ->findOrFail($id);
        $product->product_name = $request->product_name;
        $product->save();

        return response()->json(['message' => 'product updated']);
    }

    public function destroy($id)
    {
        $product = SalesProduct::where('tenant_id', Auth::user()->tenant_id)
                              ->findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'product deleted']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
        ]);

        SalesProduct::create([
            'product_name' => $request->product_name,
            'tenant_id' => Auth::user()->tenant_id,
        ]);

        return response()->json(['success' => true]);
    }

    public function getproduct(){
        $products = SalesProduct::where('tenant_id', Auth::user()->tenant_id)->get(); 
        return response()->json($products);
    }
}
