<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeederLogistic extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Password default untuk semua akun development (12345678)
        $defaultPassword = Hash::make('12345678');

        // 1. Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => $defaultPassword,
            ]
        );
        $superAdmin->assignRole('super-admin');

        // 2. HRD Officer
        $hrd = User::firstOrCreate(
            ['email' => 'hrd@gmail.com'],
            [
                'name' => 'HRD Manager',
                'password' => $defaultPassword,
            ]
        );
        $hrd->assignRole('hrd-officer');

        // 3. Finance / Keuangan
        $finance = User::firstOrCreate(
            ['email' => 'finance@gmail.com'],
            [
                'name' => 'Finance Staff',
                'password' => $defaultPassword,
            ]
        );
        $finance->assignRole('finance');

        // 4. Employee: Driver / Sopir
        $driver = User::firstOrCreate(
            ['email' => 'driver@gmail.com'],
            [
                'name' => 'Sopir Logistik (Budi)',
                'password' => $defaultPassword,
            ]
        );
        $driver->assignRole('driver');

        // 5. Employee: Warehouse Staff (Gudang / QC / Bongkar Muat)
        $warehouse = User::firstOrCreate(
            ['email' => 'gudang@gmail.com'],
            [
                'name' => 'Staf Gudang (Joko)',
                'password' => $defaultPassword,
            ]
        );
        $warehouse->assignRole('warehouse-staff');

        // 6. Access Denied (Contoh akun nonaktif / diblokir)
        $denied = User::firstOrCreate(
            ['email' => 'blocked@gmail.com'],
            [
                'name' => 'User Nonaktif',
                'password' => $defaultPassword,
            ]
        );
        $denied->assignRole('access-denied');
    }
}
