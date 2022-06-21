<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Sns;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function register(Request $request)
    {
        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'Successfully user create']);
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function index()
    {
      $data = Auth::all();
      return response()->json([
        'data' => $data,
      ], 200);
    }

    public function store(Request $request)
    {
      $data = Auth::create($request->all());
      return response()->json([
        'data' => $data,
      ], 201);
    }

    public function show($id)
    {
      $data = Auth::find($id);
      if ($data) {
        return response()->json([
          'message' => 'ok',
          'data' => $data,
        ], 200);
      }
      return response()->json([
        'message' => 'data is empty',
      ], 404);
    }

    public function update(Request $request, $id)
    {
      $update = [
        'textarea' => $request->textarea,
      ];
      $data = Auth::where('id', $id)->update($update);
      if ($data) {
        return response()->json([
          'message' => 'Successflly',
        ], 200);
      }
      return response()->json([
        'message' => 'Data not found',
      ], 404);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
