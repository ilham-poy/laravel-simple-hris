<?php

namespace Database\Seeders;

use App\Models\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employee = User::create([
            'name' => 'Employee2',
            'email' => 'employee2@gmail.com',
            'password' => bcrypt(12345678)
        ]);
        $employee->assignRole('employee');
        $employee = User::create([
            'name' => 'Employee3',
            'email' => 'employee3@gmail.com',
            'password' => bcrypt(12345678)
        ]);
        $employee->assignRole('employee');
        $employee = User::create([
            'name' => 'Employee4',
            'email' => 'employee4@gmail.com',
            'password' => bcrypt(12345678)
        ]);
        $employee->assignRole('employee');
        $employee = User::create([
            'name' => 'Employee5',
            'email' => 'employee5@gmail.com',
            'password' => bcrypt(12345678)
        ]);
        $employee->assignRole('employee');
    }
}
