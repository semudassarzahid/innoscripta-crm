<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        // Retrieve all users of the authenticated user's company
        $users = User::where('company_id', auth()->user()->company_id)->get();

        return response()->json([
            'data' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        // Only allow an admin user to create a user of the same company
        if (auth()->user()->role->name !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->company_id = auth()->user()->company_id;
        $user->role_id = 2; //which is rep user
        $user->save();

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user,
        ]);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        // Only allow the user to be retrieved if they belong to the authenticated user's company
        if ($user->company_id !== auth()->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'data' => $user,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'required|min:6',
        ]);

        $user = User::findOrFail($id);

        // Only allow the user to be updated if they belong to the authenticated user's company
        if (auth()->user()->role->name !== 'Admin' || $user->company_id !== auth()->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user,
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Only allow the user to be deleted if they belong to the authenticated user's company
        if (auth()->user()->role->name !== 'Admin' || $user->company_id !== auth()->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
