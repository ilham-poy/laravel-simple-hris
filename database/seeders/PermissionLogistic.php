<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionLogistic extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 0. Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ==========================================
        // 1. DEFINISI PERMISSION
        // ==========================================
        $permissions = [
            // 🔧 SUPER ADMIN
            'manage-roles-and-permissions',

            // 👥 HRD — Data Karyawan & Operasional
            'view-employee-data',
            'edit-employee-data',
            'create-employee',
            'delete-employee',
            'manage-employee',
            'manage-driver-licenses', // Monitoring masa berlaku SIM & SIO
            'manage-shift-schedule',  // Mengatur jadwal shift gudang & operasional
            'approve-leave',
            'approve-overtime',
            'view-performance-review',
            'view-attendance-report',

            // 💰 FINANCE / KEUANGAN
            'manage-payroll',
            'manage-trip-allowance',  // Pengaturan nominal uang jalan
            'approve-reimbursement',  // Validasi struk & bayar klaim jalan

            // 🙋‍♂️ COMMON EMPLOYEE (Permission Dasar Semua Karyawan)
            'submit-attendance',      // Absensi dasar
            'submit-leave',           // Pengajuan cuti/sakit
            'submit-overtime',        // Pengajuan lembur
            'view-own-payslip',       // Lihat slip gaji sendiri
            'view-own-profile',       // Lihat profil sendiri

            // 🚛 LOGISTICS SPECIFIC - DRIVER / SOPIR
            'submit-trip-allowance',  // Pengajuan uang jalan / perjalanan kurir-sopir
            'submit-reimbursement',   // Klaim tol, tambal ban, bensin darurat (struk)
            'view-own-license',       // Lihat status & expiry SIM sendiri

            // 📦 LOGISTICS SPECIFIC - WAREHOUSE (QC & BONGKAR MUAT)
            'view-shift-schedule',    // Lihat jadwal shift kerja/roster gudang
            'submit-shift-exchange',  // Pengajuan tukar shift antar staf gudang
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ==========================================
        // 2. DEFINISI ROLE & ASSIGNMENT
        // ==========================================

        // --- 1. SUPER ADMIN ---
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // --- 2. HRD OFFICER ---
        $hrdOfficer = Role::firstOrCreate(['name' => 'hrd-officer']);
        $hrdOfficer->givePermissionTo([
            'view-employee-data',
            'edit-employee-data',
            'create-employee',
            'delete-employee',
            'manage-employee',

            'manage-driver-licenses',
            'manage-shift-schedule',

            'approve-leave',
            'approve-overtime',
            'view-performance-review',
            'view-attendance-report',
        ]);

        // --- 3. FINANCE ---
        $finance = Role::firstOrCreate(['name' => 'finance']);
        $finance->givePermissionTo([
            'view-employee-data',
            'manage-payroll',
            'manage-trip-allowance',
            'approve-reimbursement',
            'view-attendance-report',
        ]);

        // --- 4. EMPLOYEE: DRIVER / SOPIR ---
        $driver = Role::firstOrCreate(['name' => 'driver']);
        $driver->givePermissionTo([
            // Common
            'submit-attendance',
            'submit-leave',
            'submit-overtime',
            'view-own-payslip',
            'view-own-profile',
            // Driver Specific
            'submit-trip-allowance',
            'submit-reimbursement',
            'view-own-license',
        ]);

        // --- 5. EMPLOYEE: WAREHOUSE (GUDANG / QC / BONGKAR MUAT) ---
        $warehouse = Role::firstOrCreate(['name' => 'warehouse-staff']);
        $warehouse->givePermissionTo([
            // Common
            'submit-attendance',
            'submit-leave',
            'submit-overtime',
            'view-own-payslip',
            'view-own-profile',
            // Warehouse Specific
            'view-shift-schedule',
            'submit-shift-exchange',
            // Catatan: QC atau Bongkar Muat biasanya tidak perlu fitur reimbursement jalan,
            // kecuali jika ada kebijakan khusus dari perusahaan.
        ]);

        // --- 6. ACCESS DENIED ---
        Role::firstOrCreate(['name' => 'access-denied']);
    }
}
