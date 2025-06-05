<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

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

        return new User([
            'user_code' => $row['user_code'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'] ?? '',
            'father_name' => $row['father_name'] ?? null,
            'mother_name' => $row['mother_name'] ?? null,
            'address' => $row['address'] ?? '',
            'contact_no' => $row['contact_no'],
            'guardian_name' => $row['guardian_name'],
            'guardian_relation' => $row['guardian_relation'] ?? '',
            'guardian_contact_no' => $row['guardian_contact_no'] ?? null,
            'emergency_contact' => $row['emergency_contact'] ?? null,
            'email' => $row['email'] ?? null,
            'room_number' => $row['room_number'] ?? '',
            'vehicle_detail' => $row['vehicle_detail'] ?? null,
            'occupation' => $row['occupation'] ?? '',
            'occupation_address' => $row['occupation_address'] ?? '',
            'medical_detail' => $row['medical_detail'] ?? '',
            'other_details' => $row['other_details'] ?? null,
            'joining_date' => $row['joining_date'] ?? now(),
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
