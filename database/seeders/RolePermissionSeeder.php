<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // 🔧 SUPER ADMIN — akses penuh ke sistem dan konfigurasi
        Permission::create(['name' => 'manage-roles-and-permissions']);

        // 👥 HRD OFFICER — operasional SDM dan manajemen data karyawan
        //!!! Panel ManageEmployee
        //??? untuk membuat data emplyee
        //TODO ini langkah 1
        Permission::create(['name' => 'view-employee-data']);
        Permission::create(['name' => 'edit-employee-data']);
        Permission::create(['name' => 'create-employee']);
        Permission::create(['name' => 'delete-employee']);

        //!!! 5 Panel 
        // Panel 

        Permission::create(['name' => 'manage-employee']); //mengecek validitas ,hadir absen, reimbursment

        //TODO ini langkah 4 leave di satu table dengan user id
        Permission::create(['name' => 'approve-leave']); // Melihat Pdf yang dikirim employee dan apporved
        Permission::create(['name' => 'manage-payroll']); // Hitung gaji, lembur, pajak, dan absen, dan pdf
        Permission::create(['name' => 'view-performance-review']); //Seberapa banyak ambil lembur dan absen
        Permission::create(['name' => 'approve-overtime']); // mengizinkan lembur


        // 🙋‍♂️ EMPLOYEE — akses data pribadi dan pengajuan
        //!!! Panel Employee
        //TODO ini langkah 2
        Permission::create(['name' => 'submit-attendance']); // mengirim bukti kehadiran dengan foto saat di meja pekerjaan

        //TODO ini langkah 3 leave di satu table dengan user id
        Permission::create(['name' => 'submit-leave']); // Mengirim pdf dan kalimat

        Permission::create(['name' => 'submit-overtime']); // kapan waktu lembur, status lembur
        Permission::create(['name' => 'submit-reimbursement']); // mengrim bukti berupa poto, dan waktu
        Permission::create(['name' => 'view-own-payslip']); // melihat gaji yang pdf yang diberikan hrd
        //!!! Detail Profile
        Permission::create(['name' => 'view-own-profile']); // data pribadi yg dibuat hrd


        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'hrd-officer']);
        Role::create(['name' => 'employee']);

        $roleSuperAdmin =  Role::findByName('super-admin');
        $roleHrdOfficer =  Role::findByName('hrd-officer');
        $roleEmployee =  Role::findByName('employee');

        // 🔧 SUPER ADMIN — akses penuh ke sistem dan konfigurasi
        $roleSuperAdmin->givePermissionTo('manage-roles-and-permissions');


        // 👥 HRD OFFICER — operasional HR dan manajemen data karyawan
        $roleHrdOfficer->givePermissionTo('view-employee-data');
        $roleHrdOfficer->givePermissionTo('edit-employee-data');
        $roleHrdOfficer->givePermissionTo('create-employee');
        $roleHrdOfficer->givePermissionTo('delete-employee');

        $roleHrdOfficer->givePermissionTo('manage-employee');
        $roleHrdOfficer->givePermissionTo('approve-leave');
        $roleHrdOfficer->givePermissionTo('manage-payroll');
        $roleHrdOfficer->givePermissionTo('view-performance-review');
        $roleHrdOfficer->givePermissionTo('approve-overtime');


        // 🙋‍♂️ EMPLOYEE — akses data pribadi dan pengajuan
        $roleEmployee->givePermissionTo('submit-attendance');
        $roleEmployee->givePermissionTo('submit-leave');
        $roleEmployee->givePermissionTo('submit-overtime');
        $roleEmployee->givePermissionTo('submit-reimbursement');
        $roleEmployee->givePermissionTo('view-own-profile');
        $roleEmployee->givePermissionTo('view-own-payslip');
    }
}
