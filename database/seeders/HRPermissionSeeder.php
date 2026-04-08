<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HRPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Simple permissions for the header/menu
        $simplePermissions = [
            'HR',
            'Employees',
            'Attendance',
            'Payroll',
            'Leaves',
            'Loans',
            'Departments',
            'Designations',
            'Shifts',
            'Holidays',
            'Salary Structure',
            'Biometric Devices',
        ];

        // Detailed permissions for routes/controllers (found in routes/hr.php)
        $detailedPermissions = [
            'hr.departments.view', 'hr.departments.create', 'hr.departments.edit', 'hr.departments.delete',
            'hr.designations.view', 'hr.designations.create', 'hr.designations.edit', 'hr.designations.delete',
            'hr.loans.view', 'hr.loans.create', 'hr.loans.approve', 'hr.loans.delete', 'hr.loans.schedule',
            'hr.employees.view', 'hr.employees.create', 'hr.employees.edit', 'hr.employees.delete',
            'hr.shifts.view', 'hr.shifts.create', 'hr.shifts.edit', 'hr.shifts.delete',
            'hr.holidays.view', 'hr.holidays.create', 'hr.holidays.edit', 'hr.holidays.delete',
            'hr.attendance.view', 'hr.attendance.create',
            'hr.biometric.devices.view', 'hr.biometric.devices.create', 'hr.biometric.devices.edit', 'hr.biometric.devices.delete',
            'hr.payroll.view', 'hr.payroll.create', 'hr.payroll.edit', 'hr.payroll.delete',
            'hr.leaves.view', 'hr.leaves.create', 'hr.leaves.approve',
            'hr.salary.structure.view', 'hr.salary.structure.create', 'hr.salary.structure.edit', 'hr.salary.structure.delete',
        ];

        $allPermissions = array_unique(array_merge($simplePermissions, $detailedPermissions));

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($allPermissions);
        }
    }
}
