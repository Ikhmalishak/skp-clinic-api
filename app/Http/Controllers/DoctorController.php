<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(User::where('role', 'doctor')->get(), 200);

    }

    public function store(Request $request)
    {
        $user = auth()->user(); // Get the authenticated admin
    
        // ✅ Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:20'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // ✅ Generate a secure random password
        $defaultPassword = Str::random(12); // Example: "aB4eRt9L5xWz"
        $hashedPassword = Hash::make($defaultPassword);

        DB::beginTransaction();
        try {
            // ✅ Create doctor account
            $doctor = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'role' => 'doctor',
                'company_id' => $user->company_id,
                'password' => $hashedPassword, // ✅ Hashed password
                'is_first_login' => true, // ✅ Force password reset
            ]);
    
            DB::commit();
    
            // ✅ Return one-time password in response
            return response()->json([
                'message' => 'Doctor account created successfully. Doctor must change password on first login.',
                'doctor' => [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'email' => $doctor->email,
                    'phone_number' => $doctor->phone_number,
                    'company_id' => $doctor->company_id,
                    'role' => $doctor->role,
                    'is_first_login' => $doctor->is_first_login,
                    'default_password' => $defaultPassword, // ✅ Only shown once!
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create doctor account.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $User = User::find($id);
        if (!$User)
            return response()->json(['error' => 'User not found'], 404);
        return response()->json($User, 200);
    }

    public function update(Request $request, $id)
    {
        $User = User::find($id);
        if (!$User)
            return response()->json(['error' => 'User not found'], 404);

        $User->update($request->all());
        return response()->json($User, 200);
    }

    public function destroy($id)
    {
        $User = User::find($id);
        if (!$User)
            return response()->json(['error' => 'User not found'], 404);

        $User->delete();
        return response()->json(['message' => 'User deleted'], 200);
    }
}
