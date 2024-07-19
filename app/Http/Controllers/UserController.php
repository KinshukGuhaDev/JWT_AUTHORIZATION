<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Validator;
use Illuminate\Support\Facades\Hash;




class UserController extends Controller
{
    //
    public function register(Request $request){
        $Validator = Validator::make($request->all(),[
                        'name'=>'required|string|min:5|max:100',
                        'email'=>'required|string|email|max:100|unique:users',
                        'password'=>'required|min:5|max:20'
                     ]);


        if($Validator->fails()){
            return response()->json($Validator->errors(),400);
        }


        $user = User::create([
                    'name'=>$request->name,
                    'email'=>$request->email,
                    'password'=>Hash::make($request->password)

                ]);
        return response()->json([
            'message'=>'User Registered Successfully',
            'user'=>$user
        ]);
    }

    public function login(Request $request){
        $Validator = Validator::make($request->all(),[
            'email'=>'required|string|email',
            'password'=>'required|string'
         ]);

        if($Validator->fails()){
            return response()->json($Validator->errors(),400);
        }

        if(!$token = auth()->attempt($Validator->validated())){
            return response()->json(['error'=>'Unauthorized']);
            
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token){
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth()->factory()->getTTL()*60
        ]);
    }

    public function user_access(){
        return response()->json(auth()->user());
    }

    public function token_refresh(){
        return $this->respondWithToken(auth()->refresh());
    }

    public function logout(){
        auth()->logout();
        return response()->json(['message'=>'user logged out successfully!']);
    }
}
