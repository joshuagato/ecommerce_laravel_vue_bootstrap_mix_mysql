<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{ 
  // Controller function for fetching all products
  public function index()
  {
    $allProducts = Product::inRandomOrder()->get();
    return ['products' => $allProducts];
  }

  // Controller function for getting products that belong to a category
  public function getProductsFromCategory(Request $request)
  {
    if ($request->category && $request->category !== 0 && $request->category) {
      $allCategoryProducts = Product::where('category', $request->category)->get();
      return ['categoryproducts' => $allCategoryProducts];
    }

    $allProducts = Product::inRandomOrder()->get();
    return ['categoryproducts' => $allProducts];
  }

  public function create()
  {
    //
  }

  // Controller function for creating and storing a new product
  public function store(Request $request)
  {   
    $newProduct = new Product;
    $newProduct->category = $request->category;
    $newProduct->title = $request->title;
    $newProduct->price = $request->price;
    $newProduct->image = $request->file('image')->store('product-images');
    $newProduct->description = $request->description;
    $newProduct->save();
    return ['success' => true];
  }
    
  // Controller function for fetching one product
  public function show($id)
  {
    $oneProduct = Product::where('id', $id)->first();
    return ['product' => $oneProduct];
  } 
    

  public function edit($id)
  {
    //
  }
    
  // Controller function for updating one product
  public function update(Request $request, $id)
  {
    // $oldProduct = Product::where('id', $request->id)->first();
    $oldProduct = Product::where('id', $id)->first();
    !$request->category || intval($request->category) === $oldProduct->category ? '' : $oldProduct->category = $request->category;
    !$request->title || $request->title === $oldProduct->title ? '' : $oldProduct->title = $request->title;
    !$request->price || doubleval($request->price) === $oldProduct->price ? '' : $oldProduct->price = $request->price;
    
    if ($request->hasFile('image')) {
      Storage::delete($oldProduct->image);
      $oldProduct->image = $request->file('image')->store('product-images');
    }
    
    !$request->description || $request->description === $oldProduct->description ? '' : $oldProduct->description = $request->description;
    $oldProduct->save();
    return ['success' => true, 'product' => $oldProduct];
  }
    
  // Controller function for deleting one product
  public function destroy($id)
  {
    $oldProduct = Product::where('id', $id)->first();
    $productImagePath = $oldProduct->image;

    if ($oldProduct->delete()) Storage::delete($productImagePath);
    
    return ['success' => true];
  }

}
