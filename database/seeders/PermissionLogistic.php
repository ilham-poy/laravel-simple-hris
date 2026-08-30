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
        // 1. DEFINISI PERMISSION LANGSUNG (FLAT ARRAY)
        // ==========================================
        $permissions = [
            // Role & System Management
            'role:create',
            'role:read',
            'role:update',
            'role:delete',

            // Employee
            'employee:create',
            'employee:read',
            'employee:update',
            'employee:delete',

            // Driver & Staff Licenses (SIM/SIO)
            'license:create',
            'license:read',
            'license:update',
            'license:delete',

            // Operational Shift
            'shift:create',
            'shift:read',
            'shift:update',
            'shift:delete',

            // Leave & Absence Requests
            'leave:create',
            'leave:read',
            'leave:update',
            'leave:delete',

            // Overtime
            'overtime:create',
            'overtime:read',
            'overtime:update',
            'overtime:delete',

            // Attendance
            'attendance:create',
            'attendance:read',
            'attendance:update',
            'attendance:delete',

            // Payroll & Payslips
            'payroll:create',
            'payroll:read',
            'payroll:update',
            'payroll:delete',

            // Driver Trip Allowance (Uang Jalan)
            'allowance:create',
            'allowance:read',
            'allowance:update',
            'allowance:delete',

            // Operational Reimbursement
            'reimbursement:create',
            'reimbursement:read',
            'reimbursement:update',
            'reimbursement:delete',


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
            'employee:create',
            'employee:read',
            'employee:update',
            'employee:delete',
            'license:create',
            'license:read',
            'license:update',
            'license:delete',
            'shift:create',
            'shift:read',
            'shift:update',
            'shift:delete',
            'leave:read',
            'leave:update',
            'overtime:read',
            'overtime:update',
            'attendance:create',
            'attendance:read',
            'attendance:update',
        ]);

        // --- 3. FINANCE ---
        $finance = Role::firstOrCreate(['name' => 'finance']);
        $finance->givePermissionTo([
            'payroll:create',
            'payroll:read',
            'payroll:update',
            'payroll:delete',
            'allowance:create',
            'allowance:read',
            'allowance:update',
            'allowance:delete',
            'reimbursement:create',
            'reimbursement:read',
            'reimbursement:update',
            'reimbursement:delete',
            'employee:read',
            'attendance:create',
            'attendance:read',
        ]);

        // --- 4. EMPLOYEE: DRIVER ---
        $driver = Role::firstOrCreate(['name' => 'driver']);
        $driver->givePermissionTo([
            'employee:read',
            'attendance:create',
            'attendance:read',
            'leave:create',
            'leave:read',
            'leave:delete',
            'overtime:create',
            'overtime:read',
            'payroll:read',
            'allowance:create',
            'allowance:read',
            'reimbursement:create',
            'reimbursement:read',
            'license:read',
        ]);

        // --- 5. EMPLOYEE: WAREHOUSE ---
        $warehouse = Role::firstOrCreate(['name' => 'warehouse-staff']);
        $warehouse->givePermissionTo([
            'employee:read',
            'attendance:create',
            'attendance:read',
            'leave:create',
            'leave:read',
            'leave:delete',
            'overtime:create',
            'overtime:read',
            'payroll:read',
            'shift:read',
            'shift:update',
        ]);

        // --- 6. ACCESS DENIED ---
        Role::firstOrCreate(['name' => 'access-denied']);
    }
}
