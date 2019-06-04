<?php
namespace App\Api\V1\Requests;

use Dingo\Api\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function rules()
    {
        return [
            'delivery' => 'required|integer',
            'cart' => 'required'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
?>