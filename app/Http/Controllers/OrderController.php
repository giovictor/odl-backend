<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderDetail;
use App\Cart;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;

class OrderController extends Controller
{
    public function index()
    {

    }

    public function cart()
    {
        $cart = Cart::where('user_id', auth()->user()->id)
        ->join('products', 'products.id', 'product_id')
        ->select('product_id', 'name', 'description', 'image', 'price', 'weight', 'quantity')
        ->get();
        return response()->json([
            'status' => 200,
            'data' => $cart,
        ], 200);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer'
        ]);
        $cart = new Cart([
            'user_id' => auth()->user()->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity
        ]);
        $cart->save();
        return response()->json([
            'status' => 200,
            'message' => 'Product added to cart'
        ], 200);
    }

    public function removeFromCart($id) {
        $cart = Cart::where('user_id', auth()->user()->id)->where('product_id', $id);
        if($cart->get()->count() == 0) {
            return response()->json([
                'status' => 400,
                'message' => 'Product not found in cart'
            ], 400);
        } else {
            $cart->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Product removed from cart'
            ], 200);
        }
    }

    public function store(OrderRequest $request)
    {
        $order = new Order([
            'user_id' => auth()->user()->id,
            'amount' => $request->amount,
            'billing_address' => $request->billing_address,
            'shipping_address' => $request->shipping_address,
            'contact_number' => $request->contact_number,
            'status' => $request->status
        ]);

        foreach($request->products as $product) {
            $cart = Cart::where('user_id', auth()->user()->id)->where('product_id', $product['product_id']);
            if($cart->get()->count() == 0) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Some products to be checked out were not found in cart'
                ], 400);
            } else {
                $cart->delete();
                $order->save();
                $order_detail = new OrderDetail([
                    'order_id' => $order->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity']
                ]);
                $order_detail->save();
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Order was successful',
            'order_id' => $order->id,
            'created' => $order->created_at
        ], 200);
    }

    public function show(Order $order)
    {

    }

    public function destroy(Order $order)
    {

    }
}
