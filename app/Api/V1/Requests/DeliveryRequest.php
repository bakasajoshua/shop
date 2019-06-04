<?php

namespace App\Api\V1\Requests;

use Dingo\Api\Http\FormRequest;

class DeliveryRequest extends FormRequest
{
    public function rules()
    {
        return [
            'delivery_address' => 'required',
            // 'land_mark' => 'required',
            'town' => 'required',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
