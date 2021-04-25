<?php

namespace App\Http\Controllers;

use Image;
use App\Category;
use App\Product;
use App\ProductsAttribute;
use App\ProductsImage;
use App\Coupon;
use App\User;
use App\Country;
use App\DeliveryAddress;
use App\Order;
use App\OrdersProduct;
use DB;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Exports\productsExport;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use Carbon\Carbon;

// use Intervention\Image\Image as ImageImage;

class ProductsController extends Controller
{
    //
    public function addProduct(Request $request){
         if(Session::get('adminDetails')['products_access']==0){
            return redirect('/admin/dashboard')->with('flash_message_error', 'You have no access for this module');
        }
        if($request->isMethod('post')){
            $data= $request->all();
            // echo "<pre>"; print_r($data); die;
            if(empty($data['category_id'])){
                return redirect()->back()->with('flash_message_error', 'Under category is missing');
            } 
            
            $product = new Product();
            $product->category_id=  $data['category_id'];
            $product->product_name= $data['product_name'];
            $product->product_code= $data['product_code'];
            $product->product_color=$data['product_color'];

            if(!empty($data['weight'])){
                $product->weight= $data['weight'];
            }else{
                $product->weight= 0;
            }

            if(!empty($data['description'])){
                $product->description= $data['description'];
            }else{
                $product->description= '';
            }

            if(!empty($data['sleeve'])){
                $product->sleeve= $data['sleeve'];
            }else{
                $product->sleeve= '';
            }

            if(!empty($data['pattern'])){
                $product->pattern= $data['pattern'];
            }else{
                $product->pattern= '';
            }
            

            if(!empty($data['care'])){
                $product->care= $data['care'];
            }else{
                $product->care= '';
            }
            
            $product->price= $data['price'];
            // $product->image= '';
            // Upload Image
            if($request->hasFile('image')){
            //    echo $image_tmp = Input::file('image');
            //    die;
                //  $image_tmp = Input::file('image');
                $image_tmp = $request->file('image');
                if($image_tmp->isValid()){
                    // echo "test"; die;
                    // Resize Image code 
                    $extension = $image_tmp->getClientOriginalExtension();
                    $filename= rand(11, 99999).'.'.$extension;
                    $large_image_path= 'images/backend_images/products/large/'.$filename;
                    $medium_image_path= 'images/backend_images/products/medium/'.$filename;
                    $small_image_path= 'images/backend_images/products/small/'.$filename;
                    // Resize Images
                    // Image::make($image_tmp)->save($large_image_path);
                    Image::make($image_tmp)->resize(1200, 1200)->save($large_image_path);
                    Image::make($image_tmp)->resize(600, 600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(300, 300)->save($small_image_path);
                    
                    // Store image name in products table
                    $product->image= $filename;
                }
            }

            // Upload Video
            if($request->hasFile('video')){
                // echo $video_tmp = Input::file('video'); die;
                $video_tmp = Input::file('video');
                // echo $video_name = $video_tmp->getClientOriginalName(); die;
                $video_name = $video_tmp->getClientOriginalName();
                $video_path= 'videos/';
                $video_tmp->move($video_path, $video_name);
                $product->video = $video_name;
            }

            // die;


            if(empty($data['status'])){
                $status= 0;
            }else{
                $status= 1;
            }

            if(empty($data['feature_item'])){
                $feature_item= 0;
            }else{
                $feature_item= 1;
            }
            
            $product->feature_item= $feature_item;
            $product->status= $status;
            
            $product->save();
            // return redirect()->back()->with('flash_message_success', 'Product has been added successfully');
            return redirect('/admin/view-products')->with('flash_message_success', 'Product has been added successfully');
            
            
        }
        // Categories Drop Down Start
        $categories= Category::where(['parent_id'=>0])->get();
        $categories_dropdown= "<option value='' selected disabled>Select</option>";
        foreach($categories as $cat){
            $categories_dropdown .= "<option value='".$cat->id."'>".$cat->name."</option>";
            $sub_categories= Category::where(['parent_id'=>$cat->id])->get();
            foreach ($sub_categories as $sub_cat) {
                $categories_dropdown .= "<option value='".$sub_cat->id."'>&nbsp;--&nbsp".$sub_cat->name."</option>";
            }
        }  
        // Categories Drop down ends
        
        $sleeveArray = array('Full Sleeve', 'Half Sleeve', 'Short Sleeve', 'Sleeveless');
        $patternArray = array('Checked', 'Plain', 'Printed', 'Self', 'Solid');
        
        return view('admin.products.add_product')->with(compact('categories_dropdown', 'sleeveArray', 'patternArray'));
    }

    public function editProduct(Request $request, $id= null){
        // Get Product Details
        if(Session::get('adminDetails')['products_access']==0){
            return redirect('/admin/dashboard')->with('flash_message_error', 'You have no access for this module');
        }
        if($request->isMethod('post')){
            $data= $request->all();
            // echo "<pre>"; print_r($data); die;
            if($request->hasFile('image')){
                // echo $image_tmp = Input::file('image');
                $image_tmp = $request->file('image');
             //    die;
                 if($image_tmp->isValid()){
                     // echo "test"; die;
                     // Resize Image code 
                     $extension = $image_tmp->getClientOriginalExtension();
                     $filename= rand(11, 99999).'.'.$extension;
                     $large_image_path= 'images/backend_images/products/large/'.$filename;
                     $medium_image_path= 'images/backend_images/products/medium/'.$filename;
                     $small_image_path= 'images/backend_images/products/small/'.$filename;
                     // Resize Images
                     // Image::make($image_tmp)->save($large_image_path);
                     Image::make($image_tmp)->resize(1200, 1200)->save($large_image_path);
                     Image::make($image_tmp)->resize(600, 600)->save($medium_image_path);
                     Image::make($image_tmp)->resize(300, 300)->save($small_image_path);
                     
                     // Store image name in products table
                     
                 }
             }else if(!empty($data['current_image'])) {
                 $filename= $data['current_image'];
             }else{
                 $filename= '';
             }

             // Upload Video
             if($request->hasFile('video')){
                // echo $video_tmp = Input::file('video'); die;
                $video_tmp = Input::file('video');
                // echo $video_name = $video_tmp->getClientOriginalName(); die;
                $video_name = $video_tmp->getClientOriginalName();
                $video_path= 'videos/';
                $video_tmp->move($video_path, $video_name);
                $videoName = $video_name;
            }else if(!empty($data['current_video'])) {
                $videoName= $data['current_video'];
            }else{
                $videoName= '';
            }


             if(empty($data['description'])){
                $data['description']= '';
            }

            if(empty($data['care'])){
                $data['care']= '';
            }

            if(empty($data['status'])){
                $status=0;
            }else{
                $status=1;
            }

            if(empty($data['feature_item'])){
                $feature_item=0;
            }else{
                $feature_item=1;
            }

            if(!empty($data['sleeve'])){
                $sleeve = $data['sleeve'];
            }else{
                $sleeve = '';
            }

            if(!empty($data['pattern'])){
                $pattern = $data['pattern'];
            }else{
                $pattern = '';
            }
            
            
            Product::where(['id'=>$id])->update(['category_id'=>$data['category_id'], 'product_name'=>$data['product_name'], 'product_code'=>$data['product_code'], 'product_color'=>$data['product_color'], 'description'=> $data['description'], 'care'=> $data['care'], 'price'=> $data['price'], 'weight'=> $data['weight'], 'image'=> $filename, 'video'=> $videoName, 'status'=> $status, 'feature_item'=> $feature_item, 'sleeve'=>$sleeve, 'pattern'=>$pattern]);
            

            return redirect()->back()->with('flash_message_success', 'Product has been Updated');
        }

        $productDetails = Product::where(['id'=>$id])->first();

        // Categories Drop Down Start
        $categories= Category::where(['parent_id'=>0])->get();
        $categories_dropdown= "<option value='' selected disabled>Select</option>";

        foreach($categories as $cat){
            if($cat->id ==$productDetails->category_id){
                $selected= "selected";
            }else{
                $selected ="";
            }
            $categories_dropdown .= "<option value='".$cat->id."' ".$selected.">".$cat->name."</option>";
            $sub_categories= Category::where(['parent_id'=>$cat->id])->get();
            foreach ($sub_categories as $sub_cat) {
                if($sub_cat->id== $productDetails->category_id){
                    $selected= "selected";
                }else{
                    $selected= "";
                }
                $categories_dropdown .= "<option value='".$sub_cat->id."' ".$selected.">&nbsp;--&nbsp".$sub_cat->name."</option>";
            }
        }
        // Categories Drop down ends

        $sleeveArray = array('Full Sleeve', 'Half Sleeve', 'Short Sleeve', 'Sleeveless');
        $patternArray = array('Checked', 'Plain', 'Printed', 'Self', 'Solid');

        return view('admin.products.edit_product')->with(compact('productDetails', 'categories_dropdown', 'sleeveArray', 'patternArray'));
    }

    public function viewProducts(){
        if(Session::get('adminDetails')['products_access']==0){
            return redirect('/admin/dashboard')->with('flash_message_error', 'You have no access for this module');
        }
        $products= Product::orderBy('id', 'DESC')->get();
        $products= json_decode(json_encode($products));
        // echo "<pre>"; print_r($products); die;
        foreach($products as $key=>$val){
            $category_name= Category::where(['id'=> $val->category_id])->first();
           $products[$key]->category_name= $category_name->name;
        }
        
        // echo "<pre>"; print_r($products); die;
        return view('admin.products.view_products')->with(compact('products'));
    }


    public function deleteProduct($id= null){
        if(Session::get('adminDetails')['products_access']==0){
            return redirect('/admin/dashboard')->with('flash_message_error', 'You have no access for this module');
        }
        Product::where(['id'=> $id])->delete();
        return redirect()->back()->with('flash_message_success', 'Product has been deleted Successfully');
    }


    public function deleteProductImage($id= null){
        if(Session::get('adminDetails')['products_access']==0){
            return redirect('/admin/dashboard')->with('flash_message_error', 'You have no access for this module');
        }
        // Get Product Image 
        $productImage = Product::where(['id'=> $id])->first();
        // echo $productImage->image; die;
        // Get Product Image Paths
        $large_image_path = 'images/backend_images/products/large/';
        $medium_image_path = 'images/backend_images/products/medium/';
        $small_image_path = 'images/backend_images/products/small/';
        
        // echo $large_image_path.$productImage->image; die;

        // Delete Large Image if not exists in Folder 19536.jpg
        if(file_exists($large_image_path.$productImage->image)){
            // echo "test"; die;
            unlink($large_image_path.$productImage->image);
        }

        // Delete Medium Image if not exists in Folder 
        if(file_exists($medium_image_path.$productImage->image)){
            unlink($medium_image_path.$productImage->image);
        }

        // Delete Medium Image if not exists in Folder 
        if(file_exists($small_image_path.$productImage->image)){
            unlink($small_image_path.$productImage->image);
        }

        // Delete Image from Product tables 
    
        Product::where(['id'=>$id])->update(['image'=>'']);
        return redirect()->back()->with('flash_message_success', 'Product Image has been deleted Successfully');
    } 
    
    public function deleteAltImage($id= null){
        if(Session::get('adminDetails')['products_access']==0){
            return redirect('/admin/dashboard')->with('flash_message_error', 'You have no access for this module');
        }
        // Get Product Image 
        $productImage = ProductsImage::where(['id'=> $id])->first();
        // echo $productImage->image; die;
        // Get Product Image Paths
        $large_image_path = 'images/backend_images/products/large/';
        $medium_image_path = 'images/backend_images/products/medium/';
        $small_image_path = 'images/backend_images/products/small/';
        
        // echo $large_image_path.$productImage->image; die;

        // Delete Large Image if not exists in Folder 19536.jpg
        if(file_exists($large_image_path.$productImage->image)){
            // echo "test"; die;
            unlink($large_image_path.$productImage->image);
        }

        // Delete Medium Image if not exists in Folder 
        if(file_exists($medium_image_path.$productImage->image)){
            unlink($medium_image_path.$productImage->image);
        }

        // Delete Medium Image if not exists in Folder 
        if(file_exists($small_image_path.$productImage->image)){
            unlink($small_image_path.$productImage->image);
        }

        // Delete Image from Product tables 
    
        ProductsImage::where(['id'=>$id])->delete();
        return redirect()->back()->with('flash_message_success', 'Product Alternate Image (s) has been deleted Successfully');
    }

    public function deleteProductVideo($id){
        if(Session::get('adminDetails')['products_access']==0){
            return redirect('/admin/dashboard')->with('flash_message_error', 'You have no access for this module');
        }
        // Get Video Name
        $productVideo = Product::select('video')->where('id', $id)->first();

        // Get video path
        $video_path= 'videos/';

        // Delete Video if exists in video folder
        if(file_exists($video_path.$productVideo->video)){
            unlink($video_path.$productVideo->video);
        }
        // Delete Video from products table
        Product::where('id', $id)->update(['video'=>'']);
        return redirect()->back()->with('flash_message_success', 'Product Video has been deleted Successfully');
    }

    
    public function addAttributes(Request $request, $id=null){
        if(Session::get('adminDetails')['products_access']==0){
            return redirect('/admin/dashboard')->with('flash_message_error', 'You have no access for this module');
        }
        // $productDetails= Product::where(['id'=>$id])->first();
        $productDetails= Product::with('attributes')->where(['id'=>$id])->first();
        // $productDetails = json_decode(json_encode($productDetails));
        // echo "<pre>"; print_r($productDetails); die;

        if($request->isMethod('post')){
            $data= $request->all();
            // echo "<pre>"; print_r($data); die;
            foreach($data['sku'] as $key => $val){
                if(!empty($val)){

                    // Prevent duplicate SKU Check
                    $attrCountSKU = ProductsAttribute::where('sku', $val)->count();
                    if($attrCountSKU > 0){
                        return redirect('admin/add-attributes/'.$id)->with('flash_message_error', 'SKU already exists! Please add another SKU.');
                    }

                    // Prevent duplicate Size Check 
                    $attrCountSizes = ProductsAttribute::where(['product_id'=>$id, 'size'=>$data['size'][$key]])->count();
                    
                    if($attrCountSizes> 0){
                        return redirect('admin/add-attributes/'.$id)->with('flash_message_error', '"'.$data['size'][$key].'" Size already exists for this Product! Please add another Size.');
                    }
                    $attribute= new ProductsAttribute();
                    $attribute->product_id = $id;
                    $attribute->sku = $val;
                    $attribute->size = $data['size'][$key];
                    $attribute->price = $data['price'][$key];
                    $attribute->stock = $data['stock'][$key];
                    $attribute->save();
                    
                }
            }
            return redirect('admin/add-attributes/'.$id)->with('flash_message_success', 'Product Attributes has been added Successfully');
        }
        return view('admin.products.add_attributes')->with(compact('productDetails'));
    }

    public function editAttributes(Request $request, $id= null){
        if(Session::get('adminDetails')['products_access']==0){
            return redirect('/admin/dashboard')->with('flash_message_error', 'You have no access for this module');
        }
        if($request->isMethod('post')){
            $data= $request->all();
            // echo "<pre>"; print_r($data); die;
            foreach($data['idAttr'] as $key => $attr){
                ProductsAttribute::where(['id'=>$data['idAttr'][$key]])->update(['price'=>$data['price'][$key], 'stock'=>$data['stock'][$key]]);
            }
            return redirect()->back()->with('flash_message_success', 'Products Attributes has been Updated Successfully');
        }
    }

    public function addImages(Request $request, $id=null){
        $productDetails= Product::with('attributes')->where(['id'=>$id])->first();
    
        if($request->isMethod('post')){
            //Add Images 
            $data= $request->all();
            // echo "<pre>"; print_r($data); die;
            if($request->hasFile('image')){
                 $files= $request->file('image');
                // echo "<pre>"; print_r($files); die;
                foreach ($files as $file) {
                    # code...
                    //Upload Images after resize
                    $image = new ProductsImage;
                    $extension = $file->getClientOriginalExtension();
                    $fileName= rand(111, 99999).'.'.$extension;
                    $large_image_path = "images/backend_images/products/large/".$fileName;
                    $medium_image_path = "images/backend_images/products/medium/".$fileName;
                    $small_image_path = "images/backend_images/products/small/".$fileName;
                    Image::make($file)->save($large_image_path);
                    Image::make($file)->resize(600, 600)->save($medium_image_path);
                    Image::make($file)->resize(300,300)->save($small_image_path);
                    $image->image= $fileName;
                    $image->product_id= $data['product_id'];
                    $image->save();
                }
            }


            return redirect('admin/add-images/'.$id)->with('flash_message_success', 'Product Images has been Addedd Successfully');
            
        }
        
        $productsImg= ProductsImage::where(['product_id'=> $id])->get();
        // $productsImages= json_decode(json_encode($productsImages));
        // echo "<pre>"; print_r($productsImages); die;
        $productsImages = "";
        foreach($productsImg as $img){
            $productsImages .="<tr>
            <td>".$img->id."</td>
            <td>".$img->product_id."</td>
            <td><img width='100px' src='/images/backend_images/products/small/$img->image'></td>
            <td><a rel='$img->id' rel1='delete-alt-img' href='javascript:' class='btn btn-danger btn-mini deleteRecord' title='Delete Products Image'>Delete</a></td></td>
            </tr>";
        }
        return view('admin.products.add_images')->with(compact('productDetails', 'productsImages'));
    }


    public function deleteAttribute($id = null){
        if(Session::get('adminDetails')['products_access']==0){
            return redirect('/admin/dashboard')->with('flash_message_error', 'You have no access for this module');
        }
        ProductsAttribute::where(['id'=> $id])->delete();
        return redirect()->back()->with('flash_message_success', 'Attribute has been deleted successfully');
    }

    public function products($url=null){
        // if(Session::get('adminDetails')['products_access']==0){
        //     return redirect('/admin/dashboard')->with('flash_message_error', 'You have no access for this module');
        // }
        // Show 404 Page if Category URL does not exist
        // echo $url; die;
        $countCategory = Category::where(['url'=> $url, 'status'=>1])->count();
        // echo $countCategory; die;
        if($countCategory == 0){
            abort(404);
        }

        // echo $url; die;
        $categories= Category::with('categories')->where(['parent_id'=>0])->get();
        // echo "<pre>"; print_r($categories); die;
        $categoryDetails = Category::where(['url'=> $url])->first();
        
        if($categoryDetails->parent_id == 0){
            // If url is main category url
            $subCategories = Category::where(['parent_id' => $categoryDetails->id])->get();
            // $cat_ids= "";
            $cat_ids= [];

            // foreach ($subCategories as $key => $subcat) {
            foreach ($subCategories as $subcat) {
                # code...
                // if($key == 1) $cat_ids .= ",";
                // $cat_ids .= trim($subcat->id);
                $cat_ids[]= $subcat->id;
            }
            // print_r($cat_ids); die;
            // echo $cat_ids; die;
            // $productsAll = Product::whereIn('category_id', array(17))->get();
            ///////////// NB: The products.category_id, products.status, products.id works for join in if(!empty($_GET['size'])){......} below ///////////////////

            $productsAll = Product::whereIn('products.category_id', $cat_ids)->where('products.status', '1')->orderBy('products.id', 'Desc');
            // $productsAll = json_decode(json_encode($productsAll));
            // echo "<pre>"; print_r($productsAll); die;
            $breadcrumb = "<a href='/'>Home </a>  / <a href='".$categoryDetails->url."'>".$categoryDetails->name."</a>";
        }else{
            // If url is sub category url
            $productsAll = Product::where(['products.category_id' => $categoryDetails->id])->where('products.status', '1')->orderBy('products.id', 'Desc');
            $mainCategory = Category::where('id', $categoryDetails->parent_id)->first();
            $breadcrumb = "<a href='/'>Home</a> / <a href='".$mainCategory->url."'>".$mainCategory->name."</a> / <a href='".$categoryDetails->url."'>".$categoryDetails->name."</a>";
        }

        // echo $categoryDetails->id; die;
        // $productsAll= Product::where(['category_id' => $categoryDetails->id])->paginate(3);

        if(!empty($_GET['color'])){
            $colorArray= explode('-', $_GET['color']);
            $productsAll = $productsAll->whereIn('products.product_color', $colorArray);
        }

        
        if(!empty($_GET['sleeve'])){
            $sleeveArray= explode('-', $_GET['sleeve']);
            $productsAll = $productsAll->whereIn('products.sleeve', $sleeveArray);
        }

        if(!empty($_GET['pattern'])){
            $patternArray= explode('-', $_GET['pattern']);
            $productsAll = $productsAll->whereIn('products.pattern', $patternArray);
        }

        if(!empty($_GET['size'])){
            $sizeArray= explode('-', $_GET['size']);
            $productsAll = $productsAll->join('products_attributes',
            'products_attributes.product_id', '=', 'products.id')
            ->select("products.*", 'products_attributes.product_id', 'products_attributes.size')
            ->groupBy('products_attributes.product_id')
            ->whereIn('products_attributes.size', $sizeArray);
            // $productsAll = $productsAll;
        }

        

        $productsAll = $productsAll->paginate(6);
        // $productsAll = json_decode(json_encode($productsAll));
        // echo "<pre>"; print_r($productsAll); die;
        // $colorArray = array('Black', 'Blue', 'Brown', 'Gold', 'Green', 'Pink', 'Purple', 'Red', 'Silver', 'White', 'Yellow');

        ///////////////////// Fetch the color from the Product table
        //////// NB Array Flatten is used to convert multiple array to single array
        $colorArray = Product::select('product_color')->groupBy('product_color')->get();
        $colorArray = array_flatten(json_decode(json_encode($colorArray), true));
        // echo "<pre>"; print_r($colorArray); die;

        $sleeveArray = Product::select('sleeve')->where('sleeve', '!=', '')->groupBy('sleeve')->get();
        $sleeveArray = array_flatten(json_decode(json_encode($sleeveArray), true));
        
        // echo "<pre>"; print_r($sleeveArray); die;

        $patternArray = Product::select('pattern')->where('pattern', '!=', '')->groupBy('pattern')->get();
        $patternArray = array_flatten(json_decode(json_encode($patternArray), true));
        // echo "<pre>"; print_r($patternArray); die;

        $sizesArray = ProductsAttribute::select('size')->groupBy('size')->get();
        $sizesArray = array_flatten(json_decode(json_encode($sizesArray), true));
        // echo "<pre>"; print_r($sizesArray); die;

        
        

        $meta_title = $categoryDetails->meta_title;
        $meta_description = $categoryDetails->meta_description;
        $meta_keywords = $categoryDetails->meta_keywords;
        
        return view('products.listing')->with(compact('categories', 'categoryDetails', 'productsAll', 'meta_title', 'meta_description', 'meta_keywords', 'url', 'colorArray', 'sleeveArray', 'patternArray', 'sizesArray', 'breadcrumb'));
    }

    // Before creating the filter function, the 'url' is passed into the compact of products function above and then sent to the input element for the frontside bar.php

    public function filter(Request $request){
        $data = $request->all();
        // echo "<pre>"; print_r($data); die;
        $colorUrl ="";
        if(!empty($data['colorFilter'])){
            foreach ($data['colorFilter'] as $color) {
                if(empty($colorUrl)){
                    $colorUrl = "&color=".$color;
                }else{
                    $colorUrl .= "-".$color;
                }
            }
        }
        $sleeveUrl ="";
        if(!empty($data['sleeveFilter'])){
            foreach ($data['sleeveFilter'] as $sleeve) {
                if(empty($sleeveUrl)){
                    $sleeveUrl = "&sleeve=".$sleeve;
                }else{
                    $sleeveUrl .= "-".$sleeve;
                }
            }
        }

        $patternUrl ="";
        if(!empty($data['patternFilter'])){
            foreach ($data['patternFilter'] as $pattern) {
                if(empty($patternUrl)){
                    $patternUrl = "&pattern=".$pattern;
                }else{
                    $patternUrl .= "-".$pattern;
                }
            }
        }

        $sizeUrl ="";
        if(!empty($data['sizeFilter'])){
            foreach ($data['sizeFilter'] as $size) {
                if(empty($sizeUrl)){
                    $sizeUrl = "&size=".$size;
                }else{
                    $sizeUrl .= "-".$size;
                }
            }
        }
        
        $finalUrl = "products/".$data['url']."?".$colorUrl. $sleeveUrl. $patternUrl.$sizeUrl;
        return redirect::to($finalUrl);

    }

    public function searchProducts(Request $request){
        if($request->isMethod('post')){
            $data= $request->all();
            // echo "<pre>"; print_r($data);
        
        $categories= Category::with('categories')->where(['parent_id'=>0])->get();

        $search_product = $data['product'];
        // $productsAll = Product::where('product_name', 'like', '%'.$search_product. '%')->orwhere('product_code', $search_product)->where('status', 1)->paginate(3);
        $productsAll = Product::where(function($query) use($search_product){
            $query->where('product_name', 'like', '%'.$search_product.'%')
            ->orWhere('product_code', 'like', '%'.$search_product.'%')
            ->orWhere('description', 'like', '%'.$search_product.'%')
            ->orWhere('product_color', 'like', '%'.$search_product.'%');
        })->where('status',1)->get();

        $breadcrumb = "<a href='/'>Home </a> / " .$search_product;
        
        return view('products.listing')->with(compact('categories', 'productsAll', 'search_product', 'breadcrumb'));

        }
    }


    public function product($id = null){
        // Show 404 Page if product is disabled 
        $productsCount = Product::where(['id'=>$id, 'status'=> 1])->count();
        if($productsCount == 0){
            abort(404);
        }

        // Get Product Details 
        $productDetails = Product::with('attributes')->where('id', $id)->first();
        $productDetails = json_decode(json_encode($productDetails));
        // echo "<pre>"; print_r($productDetails); die;

        $relatedProducts = Product::where('id', '!=', $id)->where(['category_id'=>$productDetails->category_id])->get();
       
        // $relatedProducts = json_decode(json_encode($relatedProducts));
        // echo "<pre>"; print_r($relatedProducts); die;

        foreach($relatedProducts->chunk(3) as $chunk){
            foreach($chunk as $item){
                // echo $item; echo "<br>";
            }

            // echo "<br><br><br>";
        }
        // die;

        // Get all Categories and Sub Categories 
        $categories = Category::with('categories')->where(['parent_id'=> 0])->get();

        $categoryDetails = Category::where('id',$productDetails->category_id)->first();
        if($categoryDetails->parent_id == 0){
            
            $breadcrumb = "<a href='/'>Home </a>  / <a href='".$categoryDetails->url."'>".$categoryDetails->name."</a> / ".$productDetails->product_name;
        }else{
           
            $mainCategory = Category::where('id', $categoryDetails->parent_id)->first();
            $breadcrumb = "<a style='color:#333' href='/'>Home</a> / <a style='color:#333' href='/products/".$mainCategory->url."'>".$mainCategory->name."</a> / <a style='color:#333' href='/products/".$categoryDetails->url."'>".$categoryDetails->name."</a> / ".$productDetails->product_name;
        }

        // Get Product Alternate Images 
        $productAltImages = ProductsImage::where('product_id', $id)->get();
        // $productAltImages = json_decode(json_encode($productAltImages));
        // echo "<pre>"; print_r($productAltImages); die;
        
        // echo $total_stock = ProductsAttribute::where('product_id', $id)->sum('stock'); die;
        $total_stock = ProductsAttribute::where('product_id', $id)->sum('stock');
        $meta_title = $productDetails->product_name;
        $meta_description = $productDetails->description;
        $meta_keyword = $productDetails->product_name;
        
        return view('products.detail')->with(compact('productDetails', 'categories', 'productAltImages', 'total_stock', 'relatedProducts', 'meta_title', 'meta_description', 'meta_keyword', 'breadcrumb'));
    }

    public function getProductPrice(Request $request){
        $data = $request->all();
        // echo "<pre>"; print_r($data); die;
        $proArr= explode("-", $data['idSize']);
        // echo $proArr[0]; echo $proArr[1]; die;
        $proAttr= ProductsAttribute::where(['product_id'=> $proArr[0], 'size'=>$proArr[1]])->first();
        $getCurrencyRates = Product::getCurrencyRates($proAttr->price);
        echo $proAttr->price."-".$getCurrencyRates['USD_Rate']."-".$getCurrencyRates['GBP_Rate']."-".$getCurrencyRates['EUR_Rate'];
        echo "#";
        echo $proAttr->stock;
    }

    public function addtocart(Request $request){
          ////////////////// Updating the cart coupon amount when others are been added to cart ////////////
        Session::forget('CouponAmount');
        Session::forget('CouponCode');

        $data= $request->all();
        // echo "<pre>"; print_r($data); die;

        if(!empty($data['wishListButton']) && $data['wishListButton']=="Wish List"){
            // echo "Wish List is Selected"; die;
            // Check if User is Logged in
            if(!Auth::check()){
                return redirect()->back()->with('flash_message_error', 'Please login to add Product in your wish list');
            }
            // Check Size is selected 
            if(empty($data['size'])){
                return redirect()->back()->with('flash_message_error', 'Please select size to add product in your wish List');
            }
            // echo "Working Fine "; die;

            // Get Product Size
            $sizeIDArr= explode("-", $data['size']);
            // echo $product_size = $sizeIDArr[1];
            $product_size = $sizeIDArr[1];

            // Get Product Price 
            $proPrice = ProductsAttribute::where(['product_id'=> $data['product_id'], 'size'=> $product_size])->first();
            // echo $product_price = $proPrice->price;
            $product_price = $proPrice->price;
            // Get User Email/Username
            $user_email = Auth::user()->email;

            // Set Quantity as 1
            $quantity =1;

            // Get Current Date
            $created_at = Carbon::now();

            // echo $data['size']; die;
            // echo $user_email;
            // echo $data['product_id'];
            // echo $data['product_color'];
            // echo $product_size; die;

            // echo $wishListCount = DB::table('wish_list')->where(['user_email'=> $user_email, 'product_id'=>$data['product_id'], 'product_color'=> $data['product_color'], 'size'=>$product_size])->count(); die;

            $wishListCount = DB::table('wish_list')->where(['user_email'=> $user_email, 'product_id'=>$data['product_id'], 'product_color'=> $data['product_color'], 'size'=>$product_size])->count(); 
            
            if($wishListCount> 0){
                return redirect()->back()->with('flash_message_error', 'Product already exists in Wish List!');
            }else{
                 // die;
                // Insert Products in the wish list
                DB::table('wish_list')->insert(['product_id'=>$data['product_id'], 'product_name'=>$data['product_name'], 'product_code'=>$data['product_code'], 'product_color'=>$data['product_color'], 'price'=>$product_price, 'size'=>$product_size, 'quantity'=>$quantity, 'user_email'=>$user_email, 'created_at'=>$created_at]);
                return redirect()->back()->with('flash_message_success', 'Product has been added in wish list');
            
            }
           
        }else{

            // If Product added from wish list 
            if(!empty($data['cartButton']) && $data['cartButton']=="Add to Cart"){
                // echo "test"; die;
                $data['quantity']= 1;
            }
            // echo "Shopping Cart is selected"; die;
            // Check Product Stock is available or not
        $product_size = explode("-", $data['size']);
        // echo $product_size[1]; die;
        // echo $data['product_id']; die;
        
        $getProductStock = ProductsAttribute::where(['product_id'=> $data['product_id'], 'size'=>$product_size[1]])->first();
        // echo $getProductStock->stock; die;
        if($getProductStock->stock<  $data['quantity']){
            return redirect()->back()->with('flash_message_error', 'Required Quantity is not available');
        }

        // if(empty($data['user_email'])){
        //     $data['user_email']= '';
        // }

        if(empty(Auth::user()->email)){
            $data['user_email']= '';
        }else{
            $data['user_email']= Auth::user()->email;
        }



        // echo $data['user_email']; die;

        // if(empty($data['session_id'])){
        //     $data['session_id']= '';
        // }
    //    echo $session_id = str_random(40); die;

        //////////// The Session Id is coming from cart ->with(compact('userCart'))////////////////
        
        $session_id= Session::get('session_id');
        if(empty($session_id)){
            $session_id = str_random(40);
             Session::put('session_id', $session_id);
        }
        // echo "test"; die;
        
        // echo $data['size']; die;


        $sizeIDArr= explode("-", $data['size']);
        $product_size = $sizeIDArr[1];

        // Check if the products already exists in cart if logged in or not

        if(empty(Auth::check())){
            $countProducts = DB::table('cart')->where(['product_id'=> $data['product_id'],'product_color'=>$data['product_color'], 'size'=>$product_size, 'session_id'=>$session_id])->count();
            if($countProducts > 0){
                return redirect()->back()->with('flash_message_error', 'Product already exists in Cart!');
            }   
        }else{
            $countProducts = DB::table('cart')->where(['product_id'=> $data['product_id'],'product_color'=>$data['product_color'], 'size'=>$product_size, 'user_email'=>$data['user_email']])->count();
            if($countProducts > 0){
                return redirect()->back()->with('flash_message_error', 'Product already exists in Cart!');
            }  
        }

        $getSKU= ProductsAttribute::select('sku')->where(['product_id'=> $data['product_id'], 'size'=>$product_size])->first();
        // echo $data['product_name']; die;
        // echo $getSKU['sku']; die;
         DB::table('cart')->insert(['product_id'=> $data['product_id'], 'product_name'=> $data['product_name'], 'product_code'=>$getSKU->sku, 'product_color'=>$data['product_color'], 'price'=>$data['price'],'size'=>$product_size, 'quantity'=>$data['quantity'], 'user_email'=>$data['user_email'], 'session_id'=>$session_id]);

        // $sizeArr = json_decode(json_encode($sizeArr));
        // echo "<pre>"; print_r($sizeArr); die;

        // echo $countProducts; die;
        // if($countProducts > 0){
        //     return redirect()->back()->with('flash_message_error', 'Product already exists in Cart!');
        // }else{
        //     $getSKU= ProductsAttribute::select('sku')->where(['product_id'=> $data['product_id'], 'size'=>$product_size])->first();

        //     DB::table('cart')->insert(['product_id'=> $data['product_id'], 'product_name'=> $data['product_name'], 'product_code'=>$getSKU->sku, 'product_color'=>$data['product_color'], 'price'=>$data['price'],'size'=>$product_size, 'quantity'=>$data['quantity'], 'user_email'=>$data['user_email'], 'session_id'=>$session_id]);
        // }

        return redirect('cart')->with('flash_message_success', 'Product has been added in Cart');
        }

        
    }

    public function cart(){
        // Pulling the products when the user is logged in....
        if(Auth::check()){
            $user_email = Auth::user()->email;
            $userCart = DB::table('cart')->where(['user_email' => $user_email])->get();
        }else{
            $session_id = Session::get('session_id');
            $userCart = DB::table('cart')->where(['session_id' => $session_id])->get();
        }

       
        foreach ($userCart as $key => $product) {
            // echo $product->product_id;  echo "<br>";
            $productDetails= Product::where('id', $product->product_id)->first();
            $userCart[$key]->image = $productDetails->image;
        }
        // echo "<pre>"; print_r($userCart); die;
        $meta_title = "Shopping Cart - E-com Website";
        $meta_description = "View Shopping Cart of E-Com Website";
        $meta_keywords = "Shopping Cart, e-com Website";
        

        return view('products.cart')->with(compact('userCart', 'meta_title', 'meta_description', 'meta_keywords'));
    }

    public function deleteCartProduct($id= null){
            ////////////////// Updating the cart coupon amount when others are been added to cart ////////////
            Session::forget('CouponAmount');
            Session::forget('CouponCode');
        // echo $id; die;
        DB::table('cart')->where('id', $id)->delete();
        return redirect('cart')->with('flash_message_success', 'Products has been deleted from Cart!');
    }

    public function wishList(){
        if(Auth::check()){
            $user_email = Auth::user()->email;
            $userWishList = DB::table('wish_list')->where('user_email', $user_email)->get();
            foreach ($userWishList as $key => $product) {
                // echo $product->product_id;  echo "<br>";
                $productDetails= Product::where('id', $product->product_id)->first();
                $userWishList[$key]->image = $productDetails->image;
            }

        }else{
            $userWishList = array();
        }
        $meta_title = "Wish List - E-com Website";
        $meta_description = "View Wish List of E-Com Website";
        $meta_keywords = "Wish List, e-com Website";
        return view('products.wish_list')->with(compact('userWishList', 'meta_title', 'meta_description', 'meta_keywords'));
    }

    public function updateCartQuantity($id= null, $quantity=null){
        Session::forget('CouponAmount');
        Session::forget('CouponCode');
        $getCartDetails= DB::table('cart')->where('id', $id)->first();
        $getAttributeStock = ProductsAttribute::where('sku', $getCartDetails->product_code)->first();
        echo $getAttributeStock->stock; echo "--";
        // die;
        // echo $updated_quantity = $getCartDetails->quantity + $quantity;
        $updated_quantity= $getCartDetails->quantity + $quantity;
        if($getAttributeStock->stock>= $updated_quantity){
            DB::table('cart')->where('id', $id)->increment('quantity', $quantity);
            return redirect('cart')->with('flash_message_success', 'Product Quantity has been Updated Successfully');
        }else{
            return redirect('cart')->with('flash_message_error', 'Required Product Quantity is not available');
        }
    }

    public function applyCoupon(Request $request){
        Session::forget('CouponAmount');
        Session::forget('CouponCode');

        $data= $request->all();
        // echo "<pre>"; print_r($data); die;
        $couponCount= Coupon::where('coupon_code', $data['coupon_code'])->count();
        if($couponCount == 0){
            return redirect()->back()->with('flash_message_error', 'Coupon does not exist');
        }else{
            // with perform other checks like Active/Inactive, Expiry date
            // echo "Success";

            // Get Coupon Details
            $couponDetails = Coupon::where('coupon_code', $data['coupon_code'])->first();

            // If coupon is Inactive
            if($couponDetails->status== 0){
                return redirect()->back()->with('flash_message_error', 'This coupon is not active');
            }
            // If coupon is Expired 
            // echo $expiry_date= $couponDetails->expiry_date; die;
            // echo $current_date= date('Y-m-d'); die;
            $expiry_date= $couponDetails->expiry_date;
            $current_date= date('Y-m-d'); 
            if($expiry_date < $current_date){
                return redirect()->back()->with('flash_message_error', 'This coupon is expired');
            }

            // echo "Success"; die;
            // Coupon is valid for Discount

            // Get Cart Total Amount 
            $session_id = Session::get('session_id');
            // $userCart = DB::table('cart')->where(['session_id'=>$session_id])->get();

            if(Auth::check()){
                $user_email = Auth::user()->email;
                $userCart= DB::table('cart')->where(['user_email' => $user_email])->get();
            }else{
                $session_id = Session::get('session_id');
                $userCart= DB::table('cart')->where(['session_id'=>$session_id])->get();
            }

            $total_amount = 0;
            foreach($userCart as $item){
               $total_amount = $total_amount + ($item->price * $item->quantity);
            }

            // Check if amount type is Fixed or Percentage 
            if($couponDetails->amount_type== "Fixed"){
                $couponAmount = $couponDetails->amount;
            }else{
                // echo $total_amount; die;
                $couponAmount = $total_amount * ($couponDetails->amount/100);
            }

            // echo $couponAmount; die;
            //Add Coupon Code & Amount in Session 
            Session::put('CouponAmount', $couponAmount);
            Session::put('CouponCode', $data['coupon_code']);
            return redirect()->back()->with('flash_message_success', 'Coupon code Successfully Applied. You are Availing Discount!');
        }
    }

    public function checkout(Request $request){
        $user_id= Auth::user()->id;
        $user_email = Auth::user()->email;
        $userDetails= User::find($user_id);
        $countries = Country::get();

        // Check if Shipping Address Exists
        $shippingCount= DeliveryAddress::where('user_id', $user_id)->count();
        $shippingDetails= array();
        if($shippingCount > 0){
            $shippingDetails = DeliveryAddress::where('user_id', $user_id)->first();
            // $shippingDetails= json_decode(json_encode($shippingDetails));
            // echo "<pre>"; print_r($shippingDetails); die;
            
        }

        // Update cart table with user email
        $session_id = Session::get('session_id');
        DB::table('cart')->where(['session_id'=> $session_id])->update(['user_email'=> $user_email]);

        if($request->isMethod('post')){
            $data= $request->all();
            // echo "<pre>"; print_r($data); die;
            
            // Return to Checkout Page if any of the field is empty
            if(empty($data['billing_name']) || empty($data['billing_address']) ||empty($data['billing_city']) || empty($data['billing_state']) || empty($data['billing_country']) || empty($data['billing_pincode']) || empty($data['billing_mobile']) || empty($data['shipping_name']) || empty($data['shipping_address']) || empty($data['shipping_city']) || empty($data['shipping_state'])|| empty($data['shipping_country']) || empty($data['shipping_pincode'])|| empty($data['shipping_mobile'])){
                return redirect()->back()->with('flash_message_error', 'Please fill all fields to Checkout!');
            }

            // Update User Details
            User::where('id', $user_id)->update(['name'=> $data['billing_name'], 'address'=> $data['billing_address'], 'city'=>$data['billing_city'], 'state'=>$data['billing_state'], 'pincode'=> $data['billing_pincode'], 'country'=>$data['billing_country'], 'mobile'=> $data['billing_mobile']]);

            // die;

            if($shippingCount > 0){
                // Update Shipping Address 
                DeliveryAddress::where('user_id', $user_id)->update(['name'=> $data['shipping_name'], 'address'=> $data['shipping_address'], 'city'=>$data['shipping_city'], 'state'=>$data['shipping_state'], 'pincode'=> $data['shipping_pincode'], 'country'=>$data['shipping_country'], 'mobile'=> $data['shipping_mobile']]);
            }else{
                // Add New Shipping Address
                $shipping = new DeliveryAddress;
                $shipping->user_id = $user_id;
                $shipping->user_email = $user_email;
                $shipping->name= $data['shipping_name'];
                $shipping->address= $data['shipping_name'];
                $shipping->city= $data['shipping_city'];
                $shipping->state= $data['shipping_state'];
                $shipping->pincode= $data['shipping_pincode'];
                $shipping->country= $data['shipping_country'];
                $shipping->mobile= $data['shipping_mobile'];
                $shipping->save();
            }
            // echo "Redirect to Order Review Page"; 
            // return redirect()->action('ProductsController@order_review');

            $pincodeCount = DB::table('pincodes')->where('pincode', $data['shipping_pincode'])->count();
            if($pincodeCount == 0){
                return redirect()->back()->with('flash_message_error', 'Your Location is not available for delivery. Please enter another location');
            }
            return redirect()->action('ProductsController@orderReview');
            
        }
        $meta_title = "Checkout - E-Com Website";
        return view('products.checkout')->with(compact('userDetails', 'countries', 'shippingDetails', 'meta_title'));
    }

    public function orderReview(){
        $user_id= Auth::user()->id;
        $user_email = Auth::user()->email;
        $userDetails= User::where('id', $user_id)->first();
        $shippingDetails= DeliveryAddress::where('user_id', $user_id)->first();
        // $userDetails = json_decode(json_encode($userDetails));
        // echo "<pre>"; print_r($userDetails); die;

        // $shippingDetails = json_decode(json_encode($shippingDetails));
        // echo "<pre>"; print_r($shippingDetails); die;

        // Fetch shipping charges 
        // echo $shippingCharges = Product::getShippingCharges($shippingDetails->country); die;


        $userCart = DB::table('cart')->where(['user_email' => $user_email])->get();
        $total_weight = 0;

        foreach ($userCart as $key => $product) {
            // echo $product->product_id;  echo "<br>";
            $productDetails= Product::where('id', $product->product_id)->first();
            $userCart[$key]->image = $productDetails->image;
            $total_weight = $total_weight + $productDetails->weight;
        }
        // echo $total_weight; die;

        // echo "<pre>"; print_r($userCart); die;
        $codpincodeCount = DB::table('cod_pincodes')->where('pincode', $shippingDetails->pincode)->count();
        $prepaidpincodeCount = DB::table('prepaid_pincodes')->where('pincode', $shippingDetails->pincode)->count();

        // echo $shippingCharges = Product::getShippingCharges($total_weight, $shippingDetails->country); die;
        $shippingCharges = Product::getShippingCharges($total_weight, $shippingDetails->country);
        Session::put('ShippingCharges', $shippingCharges);
        
        $meta_title = "Order Review - E-Com Website";
        return view('products.order_review')->with(compact('userDetails', 'shippingDetails', 'userCart', 'meta_title', 'codpincodeCount', 'prepaidpincodeCount', 'shippingCharges'));
    }

    public function placeOrder(Request $request){
        if($request->isMethod('post')){
            $data= $request->all();
            $user_id = Auth::user()->id;
            $user_email = Auth::user()->email;
            // echo "<pre>"; print_r($data); die;

            // Prevent Out of Stock Products from Ordering
            $userCart= DB::table('cart')->where('user_email', $user_email)->get();
            // $userCart = json_decode(json_encode($userCart));
            // echo "<pre>"; print_r($userCart); die;
            foreach ($userCart as $cart) {

                // echo $getAttributeCount = Product::getAttributeCount($cart->product_id, $cart->size); die;
                ///// First Method without writing the funtion in the product model /////// This can be commented from the $getAttribute and if function to the end of the if function 

                ///////////// Beginning of the getAttribute function ////////////////////
                $getAttributeCount = Product::getAttributeCount($cart->product_id, $cart->size);
                if($getAttributeCount == 0){
                    ///////////// If the stock is 0; the product must be removed from cart //////////
                    Product::deleteCartProduct($cart->product_id, $user_email);
                    return redirect('/cart')->with('flash_message_error', 'One of the product is not available. Try again.');
                }
                ///////////////// End of the getAttribute function ///////////////////////


                // echo $cart->size; die;
            //    echo $product_stock = Product::getProductStock($cart->product_id, $cart->size); die;
                $product_stock = Product::getProductStock($cart->product_id, $cart->size);
                if($product_stock == 0){
                    ///////////// If the stock is 0; the product must be removed from cart //////////
                    Product::deleteCartProduct($cart->product_id, $user_email);
                    return redirect('/cart')->with('flash_message_error', 'Product is sold out! Please choose another product.');
                }
                
                // echo "Original Stock:".$product_stock;
                // echo "Demanded Stock:".$cart->quantity; die;
                if($cart->quantity> $product_stock){
                    return redirect('/cart')->with('flash_message_error', 'Reduce Product Stock and Try Again');
                }

                // echo $product_status = Product::getProductStatus($cart->product_id); die;
                $product_status = Product::getProductStatus($cart->product_id);
                if($product_status == 0){
                    Product::deleteCartProduct($cart->product_id, $user_email);
                    return redirect('/cart')->with('flash_message_error', 'Disabled Product removed from cart. Please try placing order again');
                }

                $getCategoryId = Product::select('category_id')->where('id', $cart->product_id)->first();
                // echo $getCategoryId->category_id; die;

                // echo $category_status = Product::getCategoryStatus($getCategoryId->category_id); die;
                $category_status = Product::getCategoryStatus($getCategoryId->category_id);
                if($category_status == 0){
                    Product::deleteCartProduct($cart->product_id, $user_email);
                    return redirect('/cart')->with('flash_message_error', 'One of the product category is disabled. Please try again!');
                }

            }

            // Get Shipping Delivery Address of User
            $shippingDetails = DeliveryAddress::where(['user_email'=> $user_email])->first();
            // $shippingDetails = json_decode(json_encode($shippingDetails));
            // echo "<pre>"; print_r($shippingDetails); die;

            $pincodeCount = DB::table('pincodes')->where('pincode', $shippingDetails->pincode)->count();
            if($pincodeCount == 0){
                return redirect()->back()->with('flash_message_error', 'Your Location is not available for delivery. Please enter another location');
            }

            if(empty(Session::get('CouponCode'))){
                $coupon_code = '';
            }else{
                $coupon_code = Session::get('CouponCode');
            }

            if(empty(Session::get('CouponAmount'))){
                $coupon_amount= '';
            }else{
                $coupon_amount = Session::get('CouponAmount');
            }

            // Fetching Shipping Charges
            // $shippingCharges = Product::getShippingCharges($shippingDetails->country);

            // $data['grand_total'] =0;

            // echo Product::getGrandTotal(); die;

            // echo $data['grand_total']; die;

            $grand_total= Product::getGrandTotal(); 
            // Session::put('grand_total',$grand_total);

            $order = new Order;
            $order->user_id =       $user_id;
            $order->user_email =    $user_email;
            $order->name=           $shippingDetails->name;
            $order->address=        $shippingDetails->address;
            $order->city=           $shippingDetails->city;
            $order->state=          $shippingDetails->state;
            $order->pincode=        $shippingDetails->pincode;
            $order->country=        $shippingDetails->country;
            $order->mobile=         $shippingDetails->mobile;
            $order->coupon_code=    $coupon_code;
            $order->coupon_amount=  $coupon_amount;
            $order->order_status=   "New";
            $order->payment_method= $data['payment_method'];
            // $order->shipping_charges= $shippingCharges;
            $order->shipping_charges = Session::get('ShippingCharges');
            $order->grand_total=    $data['grand_total'];
            $order->save();
            
            $order_id= DB::getPdo()->lastInsertId();
            $cartProducts  = DB::table('cart')->where(['user_email'=> $user_email])->get();
            foreach($cartProducts as $pro){
                $cartPro = new OrdersProduct;
                $cartPro->order_id=     $order_id;
                $cartPro->user_id=      $user_id;
                $cartPro->product_id=   $pro->product_id;
                $cartPro->product_code= $pro->product_code;
                $cartPro->product_name= $pro->product_name;
                $cartPro->product_color=$pro->product_color;
                $cartPro->product_size= $pro->size;
                $product_price = Product::getProductPrice($pro->product_id, $pro->size);
                // $cartPro->product_price=$pro->price;
                $cart->product_price = $product_price;
                $cartPro->product_qty=  $pro->quantity;
                $cartPro->save();

                // Reduce Stock Script Starts
                $getProductStock = ProductsAttribute::where('sku', $pro->product_code)->first();
                // echo "Original Stock: ".$getProductStock->stock;
                // echo "Stock to reduce: ".$pro->quantity;
                $newStock = $getProductStock->stock - $pro->quantity; 
                if($newStock< 0){
                    $newStock = 0;
                }
                ProductsAttribute::where('sku', $pro->product_code)->update(['stock'=>$newStock]);
                // die;
                // Reduce Stock Scripts Ends
            }
            Session::put('order_id', $order_id);
            // Session::put('grand_total', $data['grand_total']);
            // number_format(Session::put('grand_total', $data['grand_total']), 2, ".", "");
            Session::put('grand_total', $grand_total);

            // number_format(float(Session::put('grand_total', $data['grand_total']), 2, "."));
            // number_format(Session::put('grand_total', $data['grand_total']), 2, ".", "");

            if($data['payment_method'] == "COD"){
                /* Code for Order Email Start */
                $productDetails = Order::with('orders')->where('id', $order_id)->first();
                $productDetails = json_decode(json_encode($productDetails), true);
                // echo "<pre>"; print_r($productDetails); 

                $userDetails = User::where('id', $user_id)->first();
                $userDetails = json_decode(json_encode($userDetails), true);
                // echo "<pre>"; print_r($userDetails); die;


                $email = $user_email;
                $messageData = [
                    'email'=> $email,
                    'name' => $shippingDetails->name,
                    'order_id'=> $order_id,
                    'productDetails'=> $productDetails,
                    'userDetails'=> $userDetails
                ];
                Mail::send('emails.order', $messageData, function($message) use($email){
                    $message->to($email)->subject('Order Placed - E-Com Website');
                });
                /* Code for Order Email Ends */

                // COD Redirect user to thank page after saving order
                return redirect("/thanks");
            }else{
                // PayPal - Redirect user to thanks page after saving order
                return redirect("/paypal");
            }
            // COD - Redirect user to thanks page after saving order
            // return redirect('/thanks');
        }
    }

    public function thanks(Request $request){
        $user_email= Auth::user()->email;
        DB::table('cart')->where('user_email', $user_email)->delete();
        return view('orders.thanks');
    }

    public function thanksPayal(){
        return view('orders.thanks_paypal');
    }

    public function paypal(Request $request){
        $user_email= Auth::user()->email;
        DB::table('cart')->where('user_email', $user_email)->delete();
        return view('orders.paypal');
    }

    public function cancelPayal(){
        return view('orders.cancel_paypal');
    }

    public function userOrders(){
        $user_id = Auth::user()->id;
        // $orders = Order::with('orders')->where('user_id', $user_id)->get();
        $orders = Order::with('orders')->where('user_id', $user_id)->orderBy('id', 'DESC')->get();
        // $orders= json_decode(json_encode($orders));
        // echo "<pre>"; print_r($orders); die;
        return view('orders.user_orders')->with(compact('orders'));
    }

    public function userOrderDetails($order_id){
        $user_id= Auth::user()->id;
        $orderDetails = Order::with('orders')->where(['user_id'=> $user_id, 'id'=> $order_id])->first();
        // $orderDetails = json_decode(json_encode($orderDetails));
        // echo "<pre>"; print_r($orderDetails); die;
        return view('orders.user_order_details')->with(compact('orderDetails'));

    }

    public function viewOrders(){
        if(Session::get('adminDetails')['orders_access']==0){
            return redirect('/admin/dashboard')->with('flash_message_error', 'You have no access for this module');
        }
        $orders = Order::with('orders')->orderBy('id', 'desc')->get();
        $orders = json_decode(json_encode($orders));
        // echo "<pre>"; print_r($orders); die;
        return view('admin.orders.view_orders')->with(compact('orders'));
    }

    public function viewOrderDetails($order_id){
        if(Session::get('adminDetails')['orders_access']==0){
            return redirect('/admin/dashboard')->with('flash_message_error', 'You have no access for this module');
        }
        $orderDetails = Order::with('orders')->where('id', $order_id)->first();
        $orderDetails = json_decode(json_encode($orderDetails));
        // echo "<pre>"; print_r($orderDetails); die;
        // echo $user_id= $orderDetails->user_id; die;
        $user_id= $orderDetails->user_id;
        $userDetails = User::where('id', $user_id)->first();
        // $userDetails = json_decode(json_encode($userDetails));
        // echo "<pre>"; print_r($userDetails); die;

        return view('admin.orders.order_details')->with(compact('orderDetails', 'userDetails'));
    }

    public function viewOrderInvoice($order_id){
        if(Session::get('adminDetails')['orders_access']==0){
            return redirect('/admin/dashboard')->with('flash_message_error', 'You have no access for this module');
        }
        $orderDetails = Order::with('orders')->where('id', $order_id)->first();
        $orderDetails = json_decode(json_encode($orderDetails));
        // echo "<pre>"; print_r($orderDetails); die;
        // echo $user_id= $orderDetails->user_id; die;
        $user_id= $orderDetails->user_id;
        $userDetails = User::where('id', $user_id)->first();
        // $userDetails = json_decode(json_encode($userDetails));
        // echo "<pre>"; print_r($userDetails); die;

        return view('admin.orders.order_invoice')->with(compact('orderDetails', 'userDetails'));
    }

    public function viewPDFInvoice($order_id){
        if(Session::get('adminDetails')['orders_access']==0){
            return redirect('/admin/dashboard')->with('flash_message_error', 'You have no access for this module');
        }
        $orderDetails = Order::with('orders')->where('id', $order_id)->first();
        $orderDetails = json_decode(json_encode($orderDetails));
        // echo "<pre>"; print_r($orderDetails); die;
        // echo $user_id= $orderDetails->user_id; die;
        $user_id= $orderDetails->user_id;
        $userDetails = User::where('id', $user_id)->first();
        // $userDetails = json_decode(json_encode($userDetails));
        // echo "<pre>"; print_r($userDetails); die;

        $output ='
        <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="utf-8">
                <title>Example 1</title>
                <link rel="stylesheet" href="style.css" media="all" />
            </head>
            <style>
            .clearfix:after {
                content: "";
                display: table;
                clear: both;
              }
              
              a {
                color: #5D6975;
                text-decoration: underline;
              }
              
              body {
                position: relative;
                width: 21cm;  
                height: 29.7cm; 
                margin: 0 auto; 
                color: #001028;
                background: #FFFFFF; 
                font-family: Arial, sans-serif; 
                font-size: 12px; 
                font-family: Arial;
              }
              
              header {
                padding: 10px 0;
                margin-bottom: 30px;
              }
              
              #logo {
                text-align: center;
                margin-bottom: 10px;
              }
              
              #logo img {
                width: 90px;
              }
              
              h1 {
                border-top: 1px solid  #5D6975;
                border-bottom: 1px solid  #5D6975;
                color: #5D6975;
                font-size: 2.4em;
                line-height: 1.4em;
                font-weight: normal;
                text-align: center;
                margin: 0 0 20px 0;
                background: url(dimension.png);
              }
              
              #project {
                float: left;
              }
              
              #project span {
                color: #5D6975;
                text-align: right;
                width: 52px;
                margin-right: 10px;
                display: inline-block;
                font-size: 0.8em;
              }
              
              #company {
                float: right;
                text-align: right;
              }
              
              #project div,
              #company div {
                white-space: nowrap;        
              }
              
              table {
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0;
                margin-bottom: 20px;
              }
              
              table tr:nth-child(2n-1) td {
                background: #F5F5F5;
              }
              
              table th,
              table td {
                text-align: center;
              }
              
              table th {
                padding: 5px 20px;
                color: #5D6975;
                border-bottom: 1px solid #C1CED9;
                white-space: nowrap;        
                font-weight: normal;
              }
              
              table .service,
              table .desc {
                text-align: left;
              }
              
              table td {
                padding: 20px;
                text-align: right;
              }
              
              table td.service,
              table td.desc {
                vertical-align: top;
              }
              
              table td.unit,
              table td.qty,
              table td.total {
                font-size: 1.2em;
              }
              
              table td.grand {
                border-top: 1px solid #5D6975;;
              }
              
              #notices .notice {
                color: #5D6975;
                font-size: 1.2em;
              }
              
              footer {
                color: #5D6975;
                width: 100%;
                height: 30px;
                position: absolute;
                bottom: 0;
                border-top: 1px solid #C1CED9;
                padding: 8px 0;
                text-align: center;
              }
            </style>
            <body>
                <header class="clearfix">
                <div id="logo">
                    <img src="/images/backend_images/logo.png">
                </div>
                <h1>INVOICE '.$orderDetails->id.'</h1>
                <div id="project" class="clearfix">
                    <div><span>Order ID</span> '.$orderDetails->id.'</div>
                    <div><span>Order Date</span> '.$orderDetails->created_at.'</div>
                    <div><span>Order Amount</span> '.$orderDetails->grand_total.'</div>
                    <div><span>Order Status</span> '.$orderDetails->order_status.'</div>
                    <div><span>Payment Method</span> '.$orderDetails->payment_method.'</div>
                </div>
                <div id="project" style="float:right;">
                    <div><strong>Shipping Address</strong></div>
                    <div> '.$orderDetails->name.'</div>
                    <div> '.$orderDetails->address.'</div>
                    <div> '.$orderDetails->city.',  '.$orderDetails->state.'</div>
                    <div> '.$orderDetails->pincode.'</div>
                    <div> '.$orderDetails->country.'</div>
                    <div> '.$orderDetails->mobile.'</div>
                    
                </div>
                </header>
                <main>
                <table>
                    <thead>
                    <tr>
                        <td style="width:18%"><strong>Product Code</strong></td>
                        <td style="width:18%" class="text-center"><strong> Size</strong></td>
                        <td style="width:18%" class="text-center"><strong> Color</strong></td>
                        <td style="width:18%" class="text-center"><strong> Price</strong></td>
                        <td style="width:18%" class="text-center"><strong> Qty</strong></td>
                        <td style="width:18%" class="text-right"><strong>Totals</strong></td>
                    </tr>
                    </thead>
                    <tbody>';
                    $subtotal = 0; 
                    foreach($orderDetails->orders as $pro){
                                    $output .='<tr>
                                        <td class="text-left">'. $pro->product_code .'</td>
                                        <td class="text-center">'. $pro->product_size .'</td>
                                        <td class="text-center">'. $pro->product_color .'</td>
                                        <td class="text-center">IMR '. $pro->product_price .'</td>
                                        <td class="text-center">'. $pro->product_qty .'</td>
                                        <td class="text-right">INR '. $pro->product_price * $pro->product_qty .'</td>
                                    </tr>';
                                    $subtotal= $subtotal + ($pro->product_price * $pro->product_qty); 
                                    }
                    $output .='<tr>
                        <td colspan="5">SUBTOTAL</td>
                        <td class="total">INR '.$subtotal.'</td>
                    </tr>
                    <tr>
                        <td colspan="5">SHIPPING CHARGES(+)</td>
                        <td class="total">INR '.$orderDetails->shipping_charges.'</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="grand total">COUPON DISCOUNT(-)</td>
                        <td class="grand total">INR '.$orderDetails->coupon_amount.'</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="grand total">GRAND TOTAL</td>
                        <td class="grand total">INR '.$orderDetails->grand_total.'</td>
                    </tr>
                    </tbody>
                </table>
                </main>
                <footer>
                Invoice was created on a computer and is valid without the signature and seal.
                </footer>
            </body>
            </html> 
        ';
        // Instantiate and use the dompdf class

        $dompdf = new Dompdf();
        $dompdf->loadHtml($output);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream();
    }
    
    public function updateOrderStatus(Request $request){
        if(Session::get('adminDetails')['orders_access']==0){
            return redirect('/admin/dashboard')->with('flash_message_error', 'You have no access for this module');
        }
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            Order::where('id', $data['order_id'])->update(['order_status' => $data['order_status']]);
            return redirect()->back()->with('flash_message_success', 'Order Status has been updated Successfully');
        }
    }

    public function checkPincode(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            /////////////First Method /////////////////////
            // $pincodeCount = DB::table('pincodes')->where('pincode', $data['pincode'])->count();
          
            // if($pincodeCount> 0){
            //     echo "This pincode is available for delivery";
            // }else{  
            //     echo "This pincode is not available for delivery";
            // }
            
            ////////////// Second Method /////////////////
           echo $pincodeCount = DB::table('pincodes')->where('pincode', $data['pincode'])->count();
        }
    }

    public function exportProducts(){
        return Excel::download(new productsExport, 'products.xlsx');
    }

    public function deleteWishlistProduct($id){
        DB::table('wish_list')->where('id', $id)->delete();
        return redirect()->back()->with('flash_message_success', 'Product has been deleted from wish list');
    }

    public function viewOrdersCharts(){
        $current_month_orders = Order::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
        $last_month_orders = Order::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->subMonth(1))->count();
        $last_to_last_month_orders = Order::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->subMonth(2))->count();
        return view('admin.products.view_orders_charts')->with(compact('current_month_orders', 'last_month_orders', 'last_to_last_month_orders'));
    }
}
