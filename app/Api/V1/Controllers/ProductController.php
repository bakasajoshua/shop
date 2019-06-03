<?php
namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;

class ProductController extends Controller {
    public function all(){
        return response()->json([
                'products' => Product::with('category')->get(),
                'status' => 200
                ]);
    }
}
?>