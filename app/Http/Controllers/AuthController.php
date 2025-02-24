<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     if (!Auth::attempt($credentials)) {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }

    //     $user = Auth::user();
    //     $token = $user->createToken('authToken')->plainTextToken; // ✅ This works after adding HasApiTokens

    //     return response()->json([
    //         'token' => $token,
    //         'user' => $user
    //     ], 200);
    // }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    
        $user = Auth::user();
    
        // ✅ If first login, force password change
        if ($user->is_first_login) {
            return response()->json([
                'message' => 'Password reset required',
                'force_password_change' => true
            ], 403);
        }
    
        // ✅ Generate API Token
        $token = $user->createToken('authToken')->plainTextToken;
    
        return response()->json([
            'message' => 'Login successful',
            'token' => $token, // ✅ Return the token
            'user' => $user
        ], 200);
    }    

    public function changePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }
    
        // Update the password and set is_first_login to false
        $user->update([
            'password' => Hash::make($request->new_password),
            'is_first_login' => false,
        ]);
    
        // Revoke all old tokens (force logout from other devices)
        $user->tokens()->delete();
    
        // Generate a new token after password change
        $newToken = $user->createToken('authToken')->plainTextToken;
    
        return response()->json([
            'message' => 'Password updated successfully',
            'token' => $newToken, // Return a new token
        ], 200);
    }  

}

