<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator=Validator::make($request->all(),[
            'name'=>'required|string|between:2,100',
            'email'=>'required|string|max:100|unique:users',
            'password'=>'required|string|min:6',

        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);
        }
        $user=User::create(array_merge
            ($validator->validated(),
            ['password'=>bcrypt($request->password)]));

        return response()->json(
            [
                'message'=>"User successfully register",
                'user'=>$user
            ]
            ,201);

    }
    public function login(Request $request){
        $validator=Validator::make($request->all(),[
            'email'=>'required',
            'password'=>'required|string|min:6',

        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);
        }
        if(! $token=auth()->attempt($validator->validated())){
            return response()->json(["error"=>"unauthorized"],400);

        }
        $user=User::where("email",$request->email)->first();
        $token=$user->createToken('auth-token')->plainTextToken;
        return response()->json(["token"=>$token],201);

    }
}
