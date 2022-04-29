<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class APIController extends Controller
{
    public function registerUser(Request $request){
        if($request->isMethod('post')){
            $data= $request->all();
            // echo "<pre>"; print_r($data); die;
            // Check if User Already exists
            $userCount = User::where('email', $data['email'])->count();
            if($userCount > 0){
                // return redirect()->back()->with('flash_message_error', 'Email already exists');
            }else{
                // echo "Success"; die;
                $user = new User;
                $user->name = $data['name'];
                $user->email = $data['email'];
                $user->password = bcrypt($data['password']);
                date_default_timezone_set('UTC');
                $user->created_at= date('Y-m-d H:i:s');
                $user->updated_at= date('Y-m-d H:i:s');
                $user->save();
                
                // Base64 code is used to prevent spam
                $email = $data['email'];
                $messageData = ['email'=> $data['email'], 'name'=> $data['name'], 'code'=> base64_encode($data['email'])];
                Mail::send('emails.confirmation', $messageData, function($message) use($email){
                    $message->to($email)->subject('Confirm your E-Com Account');
                });

            }
        }
    }
}
