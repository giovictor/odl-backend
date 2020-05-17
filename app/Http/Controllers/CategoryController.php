<?php

namespace App\Http\Controllers;

use Str;
use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 200,
            'data' => $categories
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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories'
        ]);

        $category = new Category([
            'name' => $request->name,
            'slug' => Str::slug($request->name, "-")
        ]);
        $category->save();

        return response()->json([
            'status' => 201,
            'message' => 'New category added'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return response()->json([
            'status' => 200,
            'data' => $category
        ], 200);
    }

    public function showProducts($slug)
    {
        $products = Category::with(['products' => function($query) {
            return $query->select(['products.id', 'name', 'description', 'price', 'weight', 'stock', 'image']);
        }])->where('slug', $slug)->get();
        return response()->json([
            'status' => 200,
            'data' => $products
        ], 200);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|unique:categories'
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, "-")
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'Category updated',
            'data' => $category
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Category deleted',
            'data' => $category
        ], 200);
    }

    public function restore($id)
    {
        $category = Category::onlyTrashed()->where('id', $id)->first();
        $category->restore();
        return response()->json([
            'status' => 200,
            'message' => 'Category restored',
            'data' => $category
        ], 200);
    }

    public function forceDestroy($id)
    {
        $category = Category::onlyTrashed()->where('id', $id)->first();
        $category->forceDelete();
        $category->products()->forceDelete();
        return response()->json([
            'status' => 200,
            'message' => 'Category permanently deleted'
        ], 200);
    }
}
