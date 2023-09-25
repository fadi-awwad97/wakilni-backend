<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductType;
use App\Models\ProductItem;
use DB;

class ProductController extends Controller
{
    public function list(Request $request)
    {
        $productTypes = ProductType::select(['product_types.*'])
            ->selectSub(function ($query) {
                $query->from('product_items')
                    ->whereRaw('product_items.product_type_id = product_types.id')
                    ->where('product_items.sold', '!=', 1)
                    ->select(DB::raw('count(*)'));
            }, 'count')

            ->where('user_id', auth()->user()->id)
            ->get();
        return response()->json(['productTypes' => $productTypes], 200);
    }

    public function list_items(Request $request)
    {
        $productItems = ProductItem::where('product_type_id', $request->product_id)->where('user_id', auth()->user()->id)->get();
        return response()->json(['productItems' => $productItems], 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);

            $imageUrl = '/images/' . $imageName;
        }
        else {
            $imageUrl = 'null';
        }

        $product = new ProductType([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'image_url' => $imageUrl,
        ]);
        if (auth()->check()) {
            $product->user_id = auth()->user()->id;
        }

        $product->save();

        return response()->json(['message' => 'Product added successfully'], 201);
    }


    public function update(Request $request)
    {
        $product = ProductType::find($request->id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $imageUrl = '/images/' . $imageName;
            $product->image_url = $imageUrl;
        }

        if ($request->name) {
            $product->name = $request->input('name');
        }

        if ($request->description) {
            $product->description = $request->input('description');
        }
        $product->save();

        return response()->json(['message' => 'Product updated successfully'], 200);
    }


    public function destroy($id)
    {
        $product = ProductType::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
    public function deleteItem($id)
    {
        $item = ProductItem::find($id);
        if (!$item) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $item->delete();
        return response()->json(['message' => 'Item deleted successfully'], 200);
    }
    

    public function updateSoldStatus(Request $request)
    {
        $item = ProductItem::find($request->id);
        if (!$item) {
            return response()->json(['message' => 'item not found'], 404);
        }

        $item->sold = !$item->sold;
        $item->save();

        return response()->json(['message' => 'Product updated successfully'], 200);
    }

    public function updateItem(Request $request)
    {
 
        $item = ProductItem::find($request->itemId);
        if (!$item) {
            return response()->json(['message' => 'item not found'], 404);
        }

        $item->serial_number = $request->serial_number;
        $item->save();

        return response()->json(['message' => 'Item updated successfully'], 200);
    }

    

    public function addItem(Request $request)
    {
        if (is_array($request->input('serial_number'))) {

            foreach ($request->input('serial_number') as $item) {
                $product = new ProductItem([
                    'serial_number' => $item,
                    'sold' => 0,
                    'product_type_id' => $request->id

                ]);
                $product->user_id = auth()->user()->id;
                $product->save();
            }
            return response()->json(['message' => 'Product added successfully'], 201);
        } else {
           
            $product = new ProductItem([
                'serial_number' => $request->input('serial_number'),
                'sold' => 0,
                'product_type_id' => $request->id

            ]);
           
            if (auth()->check()) {
                $product->user_id = auth()->user()->id;
            }
          
            $product->save();
            return response()->json(['message' => 'Product added successfully'], 201);
        }
        
    }
}
