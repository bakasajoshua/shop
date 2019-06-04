<?php
namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Api\V1\Requests\OrderRequest;
use App\Cart;
use App\Delivery;
use App\Order;
use App\OrderDetail;
use App\Payment;

class OrdersController extends Controller
{    
    public function create(OrderRequest $request) {
        $carts = auth('api')->user()->cart->whereIn('id', json_decode($request->input('cart')))->fresh('product');
        if ($carts->isEmpty())
            return response()->json([
                'message' => 'Cart data could not be found',
                'code' => 402,
            ]);
        
        $delivery = auth('api')->user()->delivery->where('id', $request->input('delivery'))->first();
        if (empty($delivery))
            return response()->json([
                'message' => 'Delivery address could not be found',
                'code' => 402,
            ]);
        
        $order = new Order;
        $order->user_id = auth('api')->user()->id;
        $order->delivery_id = $delivery->id;
        $order->save();
        $price = 0;
        foreach ($carts as $key => $cart) {
            $detail = new OrderDetail;
            $detail->order_id = $order->id;
            $detail->product_id = $cart->product_id;
            $detail->quantity = $cart->quantity;
            $detail->price = $cart->price;
            $detail->save();
            $price += $cart->price;
        }

        return response()->json($order);
    }

    public function show(){
        return response()->json([
            'orders' => auth('api')->user()->orders->fresh($with = ['details', 'details.product']),
        ]);
    }
}

?>