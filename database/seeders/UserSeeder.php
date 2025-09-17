<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin1@gmail.com',
            'password' => bcrypt(12345678)
        ]);
        $superAdmin->assignRole('super-admin');

        $hrdOfficer = User::create([
            'name' => 'HRD Officer',
            'email' => 'hrdofficer1@gmail.com',
            'password' => bcrypt(12345678)
        ]);
        $hrdOfficer->assignRole('hrd-officer');

        $employee = User::create([
            'name' => 'Employee',
            'email' => 'employee1@gmail.com',
            'password' => bcrypt(12345678)
        ]);
        $employee->assignRole('employee');
    }
}
