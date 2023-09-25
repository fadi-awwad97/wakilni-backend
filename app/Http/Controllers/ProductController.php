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
        // Return the list of product types as a JSON response
        return response()->json(['productTypes' => $productTypes], 200);
    }

    public function list_items(Request $request)
    {
        $productItems = ProductItem::where('product_type_id', $request->product_id)->where('user_id', auth()->user()->id)->get();

        // Return the list of product types as a JSON response
        return response()->json(['productItems' => $productItems], 200);
    }
    public function store(Request $request)
    {
        // Validation (add validation rules as needed)
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            // 'image' => 'required|url', // Assuming image_url is a URL field
        ]);


        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);

            $imageUrl = '/images/' . $imageName;
        }


        // Create a new product
        $product = new ProductType([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'image_url' => $imageUrl,
        ]);

        // Associate the product with the authenticated user (if using authentication)
        if (auth()->check()) {
            $product->user_id = auth()->user()->id;
        }

        // Save the product to the database
        $product->save();

        // Return a success response
        return response()->json(['message' => 'Product added successfully'], 201);
    }


    public function update(Request $request)
    {

        // Find the product by its ID
        $product = ProductType::find($request->id);

        // Check if the product exists
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Validation (add validation rules as needed)
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            // 'image_url' => 'required|url', // Assuming image_url is a URL field
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

        // Update product attributes
        if ($request->description) {
            $product->description = $request->input('description');
        }

        // Save the updated product
        $product->save();

        // Return a success response
        return response()->json(['message' => 'Product updated successfully'], 200);
    }


    public function destroy($id)
    {
        // Find the product by its ID
        $product = ProductType::find($id);

        // Check if the product exists
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Delete the product
        $product->delete();

        // Return a success response
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
    public function deleteItem($id)
    {
        // Find the product by its ID
        $item = ProductItem::find($id);

        // Check if the product exists
        if (!$item) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Delete the product
        $item->delete();

        // Return a success response
        return response()->json(['message' => 'Item deleted successfully'], 200);
    }
    

    public function updateSoldStatus(Request $request)
    {

        // Find the product by its ID
        $item = ProductItem::find($request->id);

        // Check if the product exists
        if (!$item) {
            return response()->json(['message' => 'item not found'], 404);
        }


        // Update product attributes

        $item->sold = !$item->sold;


        // Save the updated product
        $item->save();

        // Return a success response
        return response()->json(['message' => 'Product updated successfully'], 200);
    }

    public function updateItem(Request $request)
    {

        // Find the product by its ID
        $item = ProductItem::find($request->itemId);

        // Check if the product exists
        if (!$item) {
            return response()->json(['message' => 'item not found'], 404);
        }

        // Update product attributes
        $item->serial_number = $request->serial_number;

        // Save the updated product
        $item->save();

        // Return a success response
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
            // Create a new item
            $product = new ProductItem([
                'serial_number' => $request->input('serial_number'),
                'sold' => 0,
                'product_type_id' => $request->id

            ]);

            // Associate the item with the authenticated user (if using authentication)
            if (auth()->check()) {
                $product->user_id = auth()->user()->id;
            }

            // Save the item to the database
            $product->save();
            return response()->json(['message' => 'Product added successfully'], 201);
        }
        // Return a success response

    }
}
