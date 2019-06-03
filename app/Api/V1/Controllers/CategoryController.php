<?php
namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;

class CategoryController extends Controller {

    public function all(){
        return response()->json([
            'categories' => Category::get(),
            'status' => 200
        ]);
    }
}
?>