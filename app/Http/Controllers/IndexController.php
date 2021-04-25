<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\Banner;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    //
   
    public function index(){
        // In Ascending Order (By default)
        $productsAll= Product::get();

        // In Descending Order
        $productsAll= Product::orderBy('id', 'DESC')->get();

        // In Random Order
        // $productsAll = Product::inRandomOrder()->get();

        /////////////////// Showing only products where the status is 1;
        // $productsAll= Product::inRandomOrder()->where('status', 1)->where('feature_item', 1)->get();
        $productsAll= Product::inRandomOrder()->where('status', 1)->where('feature_item', 1)->paginate(6);
        
        ////////////// Basic Approach ///////////
        // Get all Categories and SubCategories 
        ////// $categories = Category::where(['parent_id'=>0])->get();
        // $categories= json_decode(json_encode($categories));
        // echo "<pre>"; print_r($categories); die;

        // $categories_menu= "";
        // foreach ($categories as $cat) {
        //     echo $cat->name; echo "<br>";
        //     $categories_menu .= "<div class='panel-heading'>
		// 							<h4 class='panel-title'>
		// 								<a data-toggle='collapse' data-parent='#accordian' href='#".$cat->id."'>
		// 									<span class='badge pull-right'><i class='fa fa-plus'></i></span>
		// 									".$cat->name."
		// 								</a>
		// 							</h4>
        //                         </div>
        //                         <div id='".$cat->id."' class='panel-collapse collapse'>
		// 							<div class='panel-body'>
		// 								<ul>";
        //                                     $sub_categories= Category::where(['parent_id'=>$cat->id])->get();
        //                                     foreach($sub_categories as $subcat){
        //                                         // echo "==========". $subcat->name; echo "<br>";
        //                                         $categories_menu .= "<li><a href='".$subcat->url."'>".$subcat->name."</a></li>";
        //                                     } 
        //                                     $categories_menu .="</ul>
		// 							</div>
		// 						</div>
        //                         ";
           
        // }

        /////// Advanced Approach ////////////
        $categories = Category::with('categories')->where(['parent_id'=>0])->get();

        $banners = Banner::where('status', '1')->get();
        $banners= json_decode(json_encode($banners));
        // echo "<pre>"; print_r($banners); die;
        
        // Meta tags
        $meta_title = "E-Shop Sample Website";
        $meta_description = "Online Shopping Site for Men, Women and Kids Clothing";
        $meta_keywords = "eshop website, online shopping, men clothing";

        return view('index')->with(compact('productsAll', 'categories', 'banners', 'meta_title', 'meta_description', 'meta_keywords'));
    }
}
