<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

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

        // Process joining date if provided
        $joiningDate = null;
        if (isset($row['joining_date']) && !empty($row['joining_date'])) {
            try {
                $joiningDate = Carbon::parse($row['joining_date']);
            } catch (\Exception $e) {
                $joiningDate = now();
            }
        } else {
            $joiningDate = now();
        }

        // Process left date if provided
        $leftDate = null;
        if (isset($row['left_date']) && !empty($row['left_date'])) {
            try {
                $leftDate = Carbon::parse($row['left_date']);
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
        return [
            'user_code' => 'required|unique:users,user_code',
            'first_name' => 'required',
            'contact_no' => 'required',
            'guardian_name' => 'required',
        ];
    }
}
