<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::where('id', '!=', 1)->orderBy('user_code')->get();
        return response()->json(['users' => $users]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_code' => 'required|unique:users',
            'first_name' => 'required',
            // 'last_name' => 'required',
            // 'address' => 'required',
            'contact_no' => 'required',
            'guardian_name' => 'required',
            // 'guardian_relation' => 'required',
            //'guardian_contact_no' => 'required',
            // 'emergency_contact' => 'required',
            // 'room_number' => 'required',
            // 'occupation' => 'required',
            // 'occupation_address' => 'required',
            // 'medical_detail' => 'required',
            // 'joining_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userData = $request->all();

        // Set a default password if not provided
        if (!isset($userData['password']) || empty($userData['password'])) {
            $userData['password'] = Hash::make('password123');
        } else {
            $userData['password'] = Hash::make($userData['password']);
        }

        $user = User::create($userData);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['user' => $user]);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_code' => 'required|unique:users,user_code,' . $id,
            'first_name' => 'required',
            // 'last_name' => 'required',
            // 'address' => 'required',
            'contact_no' => 'required',
            'guardian_name' => 'required',
            // 'guardian_relation' => 'required',
            //'guardian_contact_no' => 'required',
            // 'emergency_contact' => 'required',
            // 'room_number' => 'required',
            // 'occupation' => 'required',
            // 'occupation_address' => 'required',
            // 'medical_detail' => 'required',
            // 'joining_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userData = $request->all();

        // Update password if provided
        if (isset($userData['password']) && !empty($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']);
        } else {
            unset($userData['password']);
        }

        $user->update($userData);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Import users from a CSV or Excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            Excel::import(new UsersImport, $request->file('file'));

            return response()->json([
                'message' => 'Users imported successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error importing users',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
