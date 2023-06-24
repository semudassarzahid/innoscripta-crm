<?php
namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string',
        ]);

        $role = Role::where('name', 'Admin')->first();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role_id' => $role->id,
        ]);

        $company = Company::create([
            'name' => $validatedData['company_name'],
        ]);

        $user->company()->associate($company);
        $user->save();
        $user->load('role', 'company');

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['token' => $token, 'user_info' => $user], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = $request->user();
        $user->load('role', 'company');
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['token' => $token, 'user_info' => $user], 200);
    }
}