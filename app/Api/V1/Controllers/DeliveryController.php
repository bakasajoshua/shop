<?php
namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Api\V1\Requests\DeliveryRequest;
use App\Delivery;

class DeliveryController extends Controller
{
    public function create(DeliveryRequest $request) {
        $delivery = new Delivery;
        $delivery->user_id = auth('api')->user()->id;
        $delivery->fill($request->only(['delivery_address', 'land_mark', 'town']));
        $delivery->save();
        return response()->json(['address' => $delivery, 'code' => 201]);
    }

    public function show() {
        return response()->json(['addresses' => auth('api')->user()->delivery, 'code' => 201]);
    }
}

?>