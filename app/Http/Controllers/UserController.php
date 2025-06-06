<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', '');

        $query = User::where('id', '!=', 1);

        // Apply search if provided
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('user_code', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%")
                    ->orWhere('contact_no', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('user_code')->paginate($perPage);

        return response()->json($users);
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
            if (!$request->hasFile('file')) {
                return response()->json([
                    'message' => 'Error importing users',
                    'error' => 'No file uploaded'
                ], 422);
            }

            $file = $request->file('file');
            if (!$file->isValid()) {
                return response()->json([
                    'message' => 'Error importing users',
                    'error' => 'Invalid file upload'
                ], 422);
            }

            // Import the users
            Excel::import(new UsersImport, $file);

            return response()->json([
                'message' => 'Users imported successfully'
            ], 201);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }

            return response()->json([
                'message' => 'Validation error during import',
                'errors' => $errors
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database-related errors (like duplicate entries)
            $errorCode = $e->errorInfo[1] ?? '';
            $errorMessage = 'Database error';

            if ($errorCode == 1062) { // MySQL duplicate entry error code
                $errorMessage = 'Duplicate user code found in import file';
            }

            return response()->json([
                'message' => 'Error importing users',
                'error' => $errorMessage,
                'details' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error importing users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a template file for user import.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate(Request $request)
    {
        $type = $request->query('type', 'xlsx');

        $headers = [
            'user_code',
            'first_name',
            'last_name',
            'father_name',
            'mother_name',
            'address',
            'contact_no',
            'guardian_name',
            'guardian_relation',
            'guardian_contact_no',
            'emergency_contact',
            'email',
            'room_number',
            'vehicle_detail',
            'occupation',
            'occupation_address',
            'medical_detail',
            'other_details',
            'joining_date',
            'left_date',
            'left_remark',
            'password'
        ];

        $data = [
            $headers,
            // Add an example row
            [
                'USR001',
                'John',
                'Doe',
                'Father Name',
                'Mother Name',
                '123 Main St',
                '1234567890',
                'Guardian Name',
                'Father',
                '9876543210',
                '5555555555',
                'john@example.com',
                'A101',
                'Vehicle Details',
                'Engineer',
                'Work Address',
                'No medical issues',
                'Additional notes',
                now()->format('Y-m-d'),
                '', // left_date
                '', // left_remark
                'password123'
            ]
        ];

        $format = $type === 'xls' ? ExcelFormat::XLS : ExcelFormat::XLSX;
        $filename = 'user_import_template.' . $type;

        return Excel::download(new class($data) {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }
        }, $filename, $format);
    }
}
