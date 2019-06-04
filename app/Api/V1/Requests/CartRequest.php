<?php

namespace App\Api\V1\Requests;

use Dingo\Api\Http\FormRequest;

class CartRequest extends FormRequest
{
    public function rules()
    {
        return [
            'product_id' => 'required|integer',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
