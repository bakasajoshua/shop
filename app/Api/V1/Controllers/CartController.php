<?php
namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\CartRequest;
use App\Http\Controllers\Controller;
use App\Cart;
use App\Product;

class CartController extends Controller {
    public function index(){
        return response()->json(auth('api')->user()->cart->fresh('product'));
    }

    public function add_to_cart(CartRequest $request) {
        $product = Product::find($request->input('product_id'));
        if (empty($product))
            return response()->json([
                'message' => 'Product does not exist',
                'code' => 404
            ]);
        if ($request->input('quantity') > $product->quantity)
            return response()->json([
                'message' => 'Requested Quantity is greater than available',
                'code' => 402
            ]);
        $user = auth('api')->user();
        $cart = Cart::where('user_id', '=', $user->id)->where('product_id', '=', $product->id)->first();
        if (empty($cart)){
            $cart = new Cart;
            $cart->user_id = $user->id;
            $cart->product_id = $product->id;
            $cart->price = $product->price;
            $cart->quantity = $request->input('quantity');
        } else {
            $newCartQuantity = $cart->quantity + $request->input('quantity');
            if ($newCartQuantity > $product->quantity){
                return response()->json([
                    'message' => 'Requested Quantity is greater than available',
                    'code' => 402
                ]);
            }
            $cart->quantity = $newCartQuantity;
        }
        $cart->save();
        
        return response()->json(['cart' => $cart->fresh('product'), 'code' => 201]);
    }
}

?>