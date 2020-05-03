<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;
use Hash;
use Auth;
use App\User;
use App\Customer;
use GuzzleHttp\Client;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function generateToken($credentials)
    {
        if(!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid email or password.',
            ], 401);
        } else {
            $http = new Client();
            $response = $http->post(config('app.url')."/oauth/token", [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('passport.client_id'),
                    'client_secret' => config('passport.client_secret'),
                    'username' => $credentials['email'],
                    'password' => $credentials['password'],
                    'scope' => ''
                ]
            ]);
            return response()->json([
                'status' => 200,
                'data' => json_decode((string) $response->getBody(), true)
            ], 200);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        return $this->generateToken($credentials);
    }

    public function adminLogin(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if($user->is_admin == 1) {
            $credentials = $request->only('email', 'password');
            return $this->generateToken($credentials);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Administrator accounts are only allowed',
            ], 401);
        }
    }

    public function register(RegisterRequest $request)
    {
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $user->save();

        $customer = new Customer([
            'user_id' => $user->id,
            'billing_address' => $request->billing_address,
            'shipping_address' => $request->shipping_address,
            'birthdate' => $request->birthdate,
            'phone' => $request->phone
        ]);
        $customer->save();
        return response()->json([
            'status'  => 201,
            'message' => 'User registered successfully',
        ], 201);
    }
}
