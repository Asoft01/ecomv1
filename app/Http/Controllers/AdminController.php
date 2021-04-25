<?php

namespace App\Http\Controllers;

use App\User;
use App\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    //

    /////////////////////// Previous Code //////////////////////////
    // public function login(Request $request){
    //     // return view('admin.admin_login');
    //     if($request->isMethod('post')){
    //         $data= $request->input();

    //         // Using Admin Model from separate table
    //         echo $adminCount = Admin::where(['username' => $data['email'], 'password'=>$data['password'], 'status'=>1])->count(); die;  
    //         if(Auth::attempt(['email'=>$data['email'], 'password'=>$data['password'], 'admin'=>'1'])){
    //             // echo 'Success'; die;

    //             ///////////////////// Works with the first approach in the dashboard function ///////////////
    //             // Session::put('adminSession', $data['email']);
    //             return redirect("admin/dashboard");
    //         }else{
    //             // echo 'Failed'; die;
    //             return redirect('/admin')->with('flash_message_error', 'Invalid username or password');
    //         }
    //     }
    //     return view('admin.admin_login');
    // }
    public function login(Request $request){
        // return view('admin.admin_login');
        if($request->isMethod('post')){
            $data= $request->input();

            // Using Admin Model from separate table 
            echo $adminCount = Admin::where(['username' => $data['username'], 'password'=>md5($data['password']), 'status'=>1])->count();
            if($adminCount > 0){
                Session::put('adminSession', $data['username']);
                return redirect('/admin/dashboard');
            }else{
                // echo 'Failed'; die;
                return redirect('/admin')->with('flash_message_error', 'Invalid username or password');
            }
        }
        return view('admin.admin_login');
    }
    

    public function dashboard(){
        // echo "test"; die;

        //////////////////// First Approach to secuity against entry into the dashboard //////////////////
        // if(Session::has('adminSession')){
        //     // perform all dashboard tasks
        // }else{
        //     return redirect('/admin')->with('flash_message_error', 'Please login to access');
        // }
        return view('admin.dashboard');
    }

    public function settings(){
        $adminDetails = Admin::where(['username' => Session::get('adminSession')])->first();
        // echo "<pre>"; print_r($adminDetails); die;

        return view('admin.settings')->with(compact('adminDetails'));
    }

    //////////////////////This works for the Ajax function /////////////////////
    // Former function before the admin function is seperated from the user table

    // public function chkPassword(Request $request){
    //     $data= $request->all();
    //     $current_password= $data['current_pwd'];
    //     $check_password= User::where(['admin'=> '1'])->first();
    //     if(Hash::check($current_password, $check_password->password)){
    //         echo "true"; die;
    //     }else{
    //         echo "false"; die;
    //     }
    // }

    public function chkPassword(Request $request){
        $data= $request->all();
        // $current_password= $data['current_pwd'];
        // $check_password= Admin::where(['username'=> Session::get('adminSession')])->first();
        $adminCount = Admin::where(['username' => Session::get('adminSession'), 'password'=> md5($data['current_pwd'])])->count();
        if($adminCount== 1){
            echo "true"; die;
        }else{
            echo "false"; die;
        }
    }
    // Former code for updatePassword Admin Function

    // public function updatePassword(Request $request){
    //     if($request->isMethod('post')){
    //         $data= $request->all();
    //         // echo "<pre>"; print_r($data); die;
    //         $check_password= User::where(['email' => Auth::user()->email])->first();
    //         $current_password= $data['current_pwd'];
            
    //         if(Hash::check($current_password, $check_password->password)){
    //             // echo "true"; die;
    //             $password= bcrypt($data['new_pwd']);
    //             User::where('id', '1')->update(['password'=>$password]);
    //             return redirect('/admin/settings')->with('flash_message_success', 'Password updated Successfully');
    //         }else{
    //             // echo "false"; die;
    //             return redirect('/admin/settings')->with('flash_message_error', 'Incorrect Current Password');
    //         }
    //     }
    // }

    public function updatePassword(Request $request){
        if($request->isMethod('post')){
            $data= $request->all();
            // echo "<pre>"; print_r($data); die;
            $adminCount = Admin::where(['username' => Session::get('adminSession'), 'password'=> md5($data['current_pwd'])])->count();
            
            if($adminCount== 1){
                // echo "true"; die;
                $password= md5($data['new_pwd']);
                Admin::where('username', Session::get('adminSession'))->update(['password'=>$password]);
                return redirect('/admin/settings')->with('flash_message_success', 'Password updated Successfully');
            }else{
                // echo "false"; die;
                return redirect('/admin/settings')->with('flash_message_error', 'Incorrect Current Password');
            }
        }
    }

   
    public function logout(){
        Session::flush();
        return redirect('/admin')->with('flash_message_success', 'Logged out Successfully');
    }

    public function viewAdmins(){
        $admins= Admin::get();
        // $admins= json_decode(json_encode($admins));
        // echo "<pre>"; print_r($admins); die;
        return view('admin.admins.view_admins')->with(compact('admins'));
    }

    public function addAdmin(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            $adminCount = Admin::where('username', $data['username'])->count();
            if($adminCount> 0){
                return redirect()->back()->with('flash_message_error', 'Admin / Sub Admin already exist! Please choose another.');
            }else{
                if(empty($data['status'])){
                    $data['status']= 0;
                }
                if($data['type']== "Admin"){
                    $admin = new Admin;
                    $admin->type =     $data['type'];
                    $admin->username = $data['username'];
                    $admin->password =  md5($data['password']);
                    $admin->status =    $data['status'];
                    $admin->save();
                    return redirect()->back()->with('flash_message_success', 'Admin added successfully');
                }else if($data['type']=="Sub Admin"){
                    if(empty($data['categories_view_access'])){
                        $data['categories_view_access'] = 0;
                    }
                    if(empty($data['categories_edit_access'])){
                        $data['categories_edit_access'] = 0;
                    }
                    if(empty($data['categories_full_access'])){
                        $data['categories_full_access'] = 0;
                    }else{
                        if($data['categories_full_access']==1){
                            $data['categories_view_access']= 1;
                            $data['categories_edit_access']=1;
                        }
                    }
                    if(empty($data['products_access'])){
                        $data['products_access']= 0;
                    }
                    if(empty($data['orders_access'])){
                        $data['orders_access']= 0;
                    }
                    if(empty($data['users_access'])){
                        $data['users_access']= 0;
                    }
                    
                    
                    $admin = new Admin;
                    $admin->type =                     $data['type'];
                    $admin->username =                 $data['username'];
                    $admin->password =                 md5($data['password']);
                    $admin->status =                   $data['status'];
                    $admin->categories_view_access =   $data['categories_view_access'];
                    $admin->categories_edit_access =   $data['categories_edit_access'];
                    $admin->categories_full_access =   $data['categories_full_access'];
                    $admin->products_access =          $data['products_access'];
                    $admin->orders_access =            $data['orders_access'];
                    $admin->users_access =             $data['users_access'];
                    
                    $admin->save();
                    return redirect()->back()->with('flash_message_success', 'Sub Admin added successfully');    
                }
            }
        }
        return view('admin.admins.add_admin');
    }

    public function editAdmin(Request $request, $id){
        $adminDetails = Admin::where('id', $id)->first();
        // $adminDetails = json_decode(json_encode($adminDetails)); 
        // echo "<pre>"; print_r($adminDetails); die;

        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            if(empty($data['status'])){
                $data['status']= 0;
            }
            if($data['type']== "Admin"){
                Admin::where('username', $data['username'])->update(['password'=> md5($data['password']), 'status'=> $data['status']]);
                return redirect()->back()->with('flash_message_success', 'Admin added successfully');
            }else if($data['type']=="Sub Admin"){
                if(empty($data['categories_view_access'])){
                    $data['categories_view_access'] = 0;
                }
                if(empty($data['categories_edit_access'])){
                    $data['categories_edit_access'] = 0;
                }
                if(empty($data['categories_full_access'])){
                    $data['categories_full_access'] = 0;
                }else{
                    if($data['categories_full_access']== 1){
                        $data['categories_view_access']= 1;
                        $data['categories_edit_access']= 1;
                        
                    }
                }
                
                if(empty($data['products_access'])){
                    $data['products_access']= 0;
                }
                if(empty($data['orders_access'])){
                    $data['orders_access']= 0;
                }
                if(empty($data['users_access'])){
                    $data['users_access']= 0;
                }
                
                Admin::where('username', $data['username'])->update(['password'=> md5($data['password']), 'status'=> $data['status'], 'categories_view_access'=>$data['categories_view_access'], 'categories_edit_access'=>$data['categories_edit_access'], 'categories_full_access'=>$data['categories_full_access'], 'products_access'=>$data['products_access'], 'orders_access'=>$data['orders_access'], 'users_access'=>$data['users_access']]);
                return redirect()->back()->with('flash_message_success', 'Sub Admin Updated Successfully');
            }
        }
        return view('admin.admins.edit_admin')->with(compact('adminDetails'));
    }
}
