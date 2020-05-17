<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use App\ProductCategory;
use App\Cart;
use App\Wishlist;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use Storage;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with(['categories' => function($query) {
            return $query->select('categories.id', 'name');
        }])->orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 200,
            'data' => $products
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $product = new Product([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'weight' => $request->weight,
            'stock' => $request->stock,
            'image' => $request->file('image')->store('images')
        ]);

        $categories = explode(',', $request->categories);

        foreach($categories as $category_id) {
            $category = Category::where('id', $category_id)->get()->count();
            if($category == 0) {
                return response()->json([
                    'status' => 400,
                    'message' => 'There was an invalid category'
                ], 400);
            } else {
                $product->save();
                $productCategory = new ProductCategory([
                    'product_id' => $product->id,
                    'category_id' => $category_id
                ]);
                $productCategory->save();
            }
        }

        return response()->json([
            'status' => 201,
            'message' => 'A new product was added'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $product = Product::with(['categories' => function($query) {
            return $query->select(['categories.id', 'name']);
        }])->where('id', $product->id)->first();
        return response()->json([
            'status' => 200,
            'data' => $product
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        Storage::delete($product->image);
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'weight' => $request->weight,
            'stock' => $request->stock,
            'image' => $request->file('image')->store('images')
        ]);

        $categories = explode(',', $request->categories);

        foreach($categories as $category_id) {
            $category = Category::where('id', $category_id)->get()->count();
            if($category == 0) {
                return response()->json([
                    'status' => 400,
                    'message' => 'There was an invalid category'
                ], 400);
            } else {
                ProductCategory::where('product_id', $product->id)->delete();
                $productCategory = new ProductCategory([
                    'product_id' => $product->id,
                    'category_id' => $category_id
                ]);
                $productCategory->save();
            }
        }

        $updatedProduct = Product::with(['categories' => function($query) {
            return $query->select(['categories.id', 'name']);
        }])->where('id', $product->id)->first();

        return response()->json([
            'status' => 200,
            'message' => 'Product updated',
            'data' => $updatedProduct
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Product deleted',
            'data' => $product
        ], 200);
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->where('id', $id)->first();
        $product->restore();
        return response()->json([
            'status' => 200,
            'message' => 'Product restored',
            'data' => $product
        ], 200);
    }

    public function forceDestroy($id)
    {
        $product = Product::onlyTrashed()->where('id', $id)->first();
        $product->forceDelete();
        $product->categories()->forceDelete();
        return response()->json([
            'status' => 200,
            'message' => 'Product permanently deleted',
        ], 200);
    }

    public function wishlist()
    {
        $wishlist = Wishlist::where('user_id', auth()->user()->id)
        ->join('products', 'products.id', 'product_id')
        ->select('product_id', 'name', 'description', 'image', 'price', 'weight')
        ->get();
        return response()->json([
            'status' => 200,
            'data' => $wishlist
        ], 200);
    }

    public function addToWishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        $wishlist = new Wishlist([
            'user_id' => auth()->user()->id,
            'product_id' => $request->product_id,
        ]);
        $wishlist->save();
        return response()->json([
            'status' => 200,
            'message' => 'Product added to wishlist'
        ], 200);
    }

    public function removeFromWishlist($id) {
        $wishlist = Wishlist::where('user_id', auth()->user()->id)->where('product_id', $id);
        if($wishlist->get()->count() == 0) {
            return response()->json([
                'status' => 400,
                'message' => 'Product not found in wishlist'
            ], 400);
        } else {
            $wishlist->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Product removed from wishlist'
            ], 200);
        }
    }
}
