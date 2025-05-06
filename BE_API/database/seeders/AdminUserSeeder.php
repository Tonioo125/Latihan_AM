<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\University;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure there's at least one university
        $university = University::first();
        if (!$university) {
            $university = University::create([
                'id' => Str::uuid(),
                'pt_code' => 'UNIV001',
                'name' => 'Example University',
                'address' => '123 Campus Rd',
                'website' => 'https://example.edu'
            ]);
        }

        User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'id' => Str::uuid(),
            'username' => 'admin_user',
            'password' => Hash::make('password123'),
            'university_id' => $university->id,
            'phone' => '08123456789',
            'role' => 'Admin',
            'approval' => 'Approved',
        ]);
    }
}
