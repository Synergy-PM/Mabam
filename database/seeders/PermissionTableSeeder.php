<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $permissions = [

            ['name' => 'user_view', 'guard_name' => 'web', 'group_name' => 'User'],
            ['name' => 'user_create', 'guard_name' => 'web', 'group_name' => 'User'],
            ['name' => 'user_edit', 'guard_name' => 'web', 'group_name' => 'User'],
            ['name' => 'user_trash', 'guard_name' => 'web', 'group_name' => 'User'],
            ['name' => 'user_trash_view', 'guard_name' => 'web', 'group_name' => 'User'],
            ['name' => 'user_restore', 'guard_name' => 'web', 'group_name' => 'User'],

            ['name' => 'role_view', 'guard_name' => 'web', 'group_name' => 'Role'],
            ['name' => 'role_create', 'guard_name' => 'web', 'group_name' => 'Role'],
            ['name' => 'role_edit', 'guard_name' => 'web', 'group_name' => 'Role'],
            ['name' => 'role_trash', 'guard_name' => 'web', 'group_name' => 'Role'],
            ['name' => 'role_trash_view', 'guard_name' => 'web', 'group_name' => 'Role'],
            ['name' => 'role_restore', 'guard_name' => 'web', 'group_name' => 'Role'],
            
            ['name' => 'supplier_view', 'guard_name' => 'web', 'group_name' => 'Supplier'],
            ['name' => 'supplier_create', 'guard_name' => 'web', 'group_name' => 'Supplier'],
            ['name' => 'supplier_edit', 'guard_name' => 'web', 'group_name' => 'Supplier'],
            ['name' => 'supplier_trash', 'guard_name' => 'web', 'group_name' => 'Supplier'],
            ['name' => 'supplier_trash_view', 'guard_name' => 'web', 'group_name' => 'Supplier'],
            ['name' => 'supplier_restore', 'guard_name' => 'web', 'group_name' => 'Supplier'],

            ['name' => 'dealer_view', 'guard_name' => 'web', 'group_name' => 'Dealers'],
            ['name' => 'dealer_create', 'guard_name' => 'web', 'group_name' => 'Dealers'],
            ['name' => 'dealer_edit', 'guard_name' => 'web', 'group_name' => 'Dealers'],
            ['name' => 'dealer_trash', 'guard_name' => 'web', 'group_name' => 'Dealers'],
            ['name' => 'dealer_trash_view', 'guard_name' => 'web', 'group_name' => 'Dealers'],
            ['name' => 'dealer_restore', 'guard_name' => 'web', 'group_name' => 'Dealers'],

            ['name' => 'payable_view', 'guard_name' => 'web', 'group_name' => 'Payables'],
            ['name' => 'payable_create', 'guard_name' => 'web', 'group_name' => 'Payables'],
            ['name' => 'payable_edit', 'guard_name' => 'web', 'group_name' => 'Payables'],
            ['name' => 'payable_trash', 'guard_name' => 'web', 'group_name' => 'Payables'],
            ['name' => 'payable_trash_view', 'guard_name' => 'web', 'group_name' => 'Payables'],
            ['name' => 'payable_restore', 'guard_name' => 'web', 'group_name' => 'Payables'],

            ['name' => 'purchasing_rate_view', 'guard_name' => 'web', 'group_name' => 'Purchasing Rate'],
            ['name' => 'purchasing_rate_create', 'guard_name' => 'web', 'group_name' => 'Purchasing Rate'],
            ['name' => 'purchasing_rate_edit', 'guard_name' => 'web', 'group_name' => 'Purchasing Rate'],
            ['name' => 'purchasing_rate_trash', 'guard_name' => 'web', 'group_name' => 'Purchasing Rate'],
            ['name' => 'purchasing_rate_trash_view', 'guard_name' => 'web', 'group_name' => 'Purchasing Rate'],
            ['name' => 'purchasing_rate_restore', 'guard_name' => 'web', 'group_name' => 'Purchasing Rate'],
            
            ['name' => 'cash_book_view', 'guard_name' => 'web', 'group_name' => 'Cash book'],
            ['name' => 'cash_book_create', 'guard_name' => 'web', 'group_name' => 'Cash book'],
            ['name' => 'cash_book_edit', 'guard_name' => 'web', 'group_name' => 'Cash book'],
            ['name' => 'cash_book_trash', 'guard_name' => 'web', 'group_name' => 'Cash book'],
            ['name' => 'cash_book_trash_view', 'guard_name' => 'web', 'group_name' => 'Cash book'],
            ['name' => 'cash_book_restore', 'guard_name' => 'web', 'group_name' => 'Cash book'],
             
            ['name' => 'ledger_payable_view', 'guard_name' => 'web', 'group_name' => 'Reports'],
            ['name' => 'ledger_receivable_view', 'guard_name' => 'web', 'group_name' => 'Reports'],
            ['name' => 'stock_report_view', 'guard_name' => 'web', 'group_name' => 'Reports'],
            ['name' => 'daily_report_view', 'guard_name' => 'web', 'group_name' => 'Reports'],

            ['name' => 'user_activity_view', 'guard_name' => 'web', 'group_name' => 'User Activity'],

             ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }
    }
}
