<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Set default password if not provided
        $password = isset($row['password']) && !empty($row['password'])
            ? Hash::make($row['password'])
            : Hash::make('welcome');

        // Process joining date if provided - ensure SQL Server compatible format
        $joiningDate = null;
        if (isset($row['joining_date']) && !empty($row['joining_date'])) {
            try {
                // Parse the date and format it in a SQL Server compatible format
                $joiningDate = Carbon::parse($row['joining_date'])->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $joiningDate = now()->format('Y-m-d H:i:s');
            }
        } else {
            $joiningDate = now()->format('Y-m-d H:i:s');
        }

        // Process left date if provided - ensure SQL Server compatible format
        $leftDate = null;
        if (isset($row['left_date']) && !empty($row['left_date'])) {
            try {
                // Parse the date and format it in a SQL Server compatible format
                $leftDate = Carbon::parse($row['left_date'])->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $leftDate = null;
            }
        }

        return new User([
            'user_code' => $row['user_code'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'] ?? '',
            'father_name' => $row['father_name'] ?? null,
            'mother_name' => $row['mother_name'] ?? null,
            'address' => $row['address'] ?? null,
            'contact_no' => $row['contact_no'],
            'guardian_name' => $row['guardian_name'],
            'guardian_relation' => $row['guardian_relation'] ?? null,
            'guardian_contact_no' => $row['guardian_contact_no'] ?? null,
            'emergency_contact' => $row['emergency_contact'] ?? null,
            'email' => $row['email'] ?? null,
            'room_number' => $row['room_number'] ?? null,
            'vehicle_detail' => $row['vehicle_detail'] ?? null,
            'occupation' => $row['occupation'] ?? null,
            'occupation_address' => $row['occupation_address'] ?? null,
            'medical_detail' => $row['medical_detail'] ?? null,
            'other_details' => $row['other_details'] ?? null,
            'left_date' => $leftDate,
            'left_remark' => $row['left_remark'] ?? null,
            'joining_date' => $joiningDate,
            'password' => $password,
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        // For SQL Server, we need to handle case-insensitive uniqueness differently
        $connection = DB::connection()->getDriverName();

        if ($connection === 'sqlsrv') {
            // For SQL Server, we'll check uniqueness in a case-insensitive way
            return [
                'user_code' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $exists = User::whereRaw('LOWER(user_code) = ?', [strtolower($value)])->exists();
                        if ($exists) {
                            $fail('The user code has already been taken.');
                        }
                    }
                ],
                'first_name' => 'required',
                'contact_no' => 'required',
                'guardian_name' => 'required',
            ];
        }

        // Default rules for other database systems
        return [
            'user_code' => 'required|unique:users,user_code',
            'first_name' => 'required',
            'contact_no' => 'required',
            'guardian_name' => 'required',
        ];
    }
}
