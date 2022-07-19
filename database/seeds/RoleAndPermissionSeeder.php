<?php

use App\Constants\Permissions;
use App\Constants\Roles;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => Permissions::CAN_ASSIGN_USER_ROLE]);
        Permission::create(['name' => Permissions::CAN_CREATE_EXPENDITURE_CATEGORY]);
        Permission::create(['name' => Permissions::CAN_CREATE_EXPENDITURE_DETAIL]);
        Permission::create(['name' => Permissions::CAN_CREATE_EXPENDITURE_ITEM]);
        Permission::create(['name' => Permissions::CAN_CREATE_INCOME_ACTIVITY]);
        Permission::create(['name' => Permissions::CAN_CREATE_ORGANISATION]);
        Permission::create(['name' => Permissions::CAN_CREATE_PAYMENT_CATEGORY]);
        Permission::create(['name' => Permissions::CAN_CREATE_PAYMENT_ITEM]);
        Permission::create(['name' => Permissions::CAN_CREATE_USER]);
        Permission::create(['name' => Permissions::CAN_DELETE_ORGANISATION]);
        Permission::create(['name' => Permissions::CAN_DELETE_ROLE]);
        Permission::create(['name' => Permissions::CAN_CREATE_USER_PAYMENT]);
        Permission::create(['name' => Permissions::CAN_CREATE_USER_SAVING]);
        Permission::create(['name' => Permissions::CAN_DELETE_EXPENDITURE_CATEGORY]);
        Permission::create(['name' => Permissions::CAN_DELETE_EXPENDITURE_DETAIL]);
        Permission::create(['name' => Permissions::CAN_DELETE_EXPENDITURE_ITEM]);
        Permission::create(['name' => Permissions::CAN_DELETE_INCOME_ACTIVITY]);
        Permission::create(['name' => Permissions::CAN_DELETE_PAYMENT_ITEM]);
        Permission::create(['name' => Permissions::CAN_DELETE_USER]);
        Permission::create(['name' => Permissions::CAN_DELETE_USER_PAYMENT]);
        Permission::create(['name' => Permissions::CAN_DELETE_USER_SAVING]);
        Permission::create(['name' => Permissions::CAN_GET_EXPENDITURE_CATEGORIES]);
        Permission::create(['name' => Permissions::CAN_GET_EXPENDITURE_CATEGORY]);
        Permission::create(['name' => Permissions::CAN_GET_EXPENDITURE_DETAIL]);
        Permission::create(['name' => Permissions::CAN_GET_EXPENDITURE_DETAILS]);
        Permission::create(['name' => Permissions::CAN_GET_EXPENDITURE_ITEM]);
        Permission::create(['name' => Permissions::CAN_GET_EXPENDITURE_ITEMS]);
        Permission::create(['name' => Permissions::CAN_GET_INCOME_ACTIVITIES]);
        Permission::create(['name' => Permissions::CAN_GET_INCOME_ACTIVITY]);
        Permission::create(['name' => Permissions::CAN_GET_ORGANISATION]);
        Permission::create(['name' => Permissions::CAN_GET_PAYMENT_CATEGORIES]);
        Permission::create(['name' => Permissions::CAN_GET_PAYMENT_CATEGORY]);
        Permission::create(['name' => Permissions::CAN_GET_PAYMENT_ITEM]);
        Permission::create(['name' => Permissions::CAN_GET_PAYMENT_ITEMS]);
        Permission::create(['name' => Permissions::CAN_GET_USER]);
        Permission::create(['name' => Permissions::CAN_GET_USER_PAYMENT]);
        Permission::create(['name' => Permissions::CAN_GET_USER_PAYMENTS]);
        Permission::create(['name' => Permissions::CAN_GET_USER_SAVING]);
        Permission::create(['name' => Permissions::CAN_GET_USER_SAVINGS]);
        Permission::create(['name' => Permissions::CAN_GET_USERS]);
        Permission::create(['name' => Permissions::CAN_REMOVE_USER_ROLE]);
        Permission::create(['name' => Permissions::CAN_UPDATE_EXPENDITURE_CATEGORY]);
        Permission::create(['name' => Permissions::CAN_UPDATE_EXPENDITURE_DETAIL]);
        Permission::create(['name' => Permissions::CAN_UPDATE_EXPENDITURE_ITEM]);
        Permission::create(['name' => Permissions::CAN_UPDATE_INCOME_ACTIVITY]);
        Permission::create(['name' => Permissions::CAN_UPDATE_ORGANISATION]);
        Permission::create(['name' => Permissions::CAN_UPDATE_PAYMENT_CATEGORY]);
        Permission::create(['name' => Permissions::CAN_UPDATE_USER]);
        Permission::create(['name' => Permissions::CAN_UPDATE_USER_PAYMENT]);
        Permission::create(['name' => Permissions::CAN_UPDATE_USER_SAVING]);
        Permission::create(['name' => Permissions::CAN_DELETE_PAYMENT_CATEGORY]);
        Permission::create(['name' => Permissions::CAN_UPDATE_PAYMENT_ITEM]);
        Permission::create(['name' => Permissions::CAN_APPROVE_USER_SAVING]);
        Permission::create(['name' => Permissions::CAN_APPROVE_INCOME_FOR_INCOME_ACTIVITY]);
        Permission::create(['name' => Permissions::CAN_APPROVE_INCOME_FOR_EXPENDITURE_ITEM]);
        Permission::create(['name' => Permissions::CAN_APPROVE_USER_PAYMENT]);


        $adminRole = Role::create(['name' => Roles::ADMIN]);
        $financialSecretaryRole = Role::create(['name' => Roles::FINANCIAL_SECRETARY]);
        $treasurerRole = Role::create(['name' => Roles::TREASURER]);
        $auditorRole = Role::create(['name' => Roles::AUDITOR]);
        $presidentRole = Role::create(['name' => Roles::PRESIDENT]);

        $adminRole->givePermissionTo([
            Permissions::CAN_DELETE_ORGANISATION,
            Permissions::CAN_DELETE_ROLE
        ]);

        $presidentRole->givePermissionTo([
            Permissions::CAN_CREATE_ORGANISATION,
            Permissions::CAN_GET_ORGANISATION,
            Permissions::CAN_UPDATE_ORGANISATION,
            Permissions::CAN_CREATE_USER,
            Permissions::CAN_ASSIGN_USER_ROLE,
            Permissions::CAN_REMOVE_USER_ROLE,
            Permissions::CAN_CREATE_INCOME_ACTIVITY,
            Permissions::CAN_GET_USER_SAVINGS,
            Permissions::CAN_CREATE_PAYMENT_CATEGORY,
            Permissions::CAN_CREATE_EXPENDITURE_CATEGORY,
            Permissions::CAN_UPDATE_USER,
            Permissions::CAN_DELETE_USER,
            Permissions::CAN_DELETE_EXPENDITURE_CATEGORY,
            Permissions::CAN_DELETE_EXPENDITURE_DETAIL,
            Permissions::CAN_DELETE_EXPENDITURE_ITEM,
            Permissions::CAN_DELETE_INCOME_ACTIVITY,
            Permissions::CAN_DELETE_PAYMENT_CATEGORY,
            Permissions::CAN_DELETE_PAYMENT_ITEM,
            Permissions::CAN_DELETE_USER_PAYMENT,
            Permissions::CAN_DELETE_USER_SAVING
        ]);

        $financialSecretaryRole->givePermissionTo([
            Permissions::CAN_CREATE_EXPENDITURE_CATEGORY,
            Permissions::CAN_CREATE_EXPENDITURE_DETAIL,
            Permissions::CAN_CREATE_EXPENDITURE_ITEM,
            Permissions::CAN_CREATE_INCOME_ACTIVITY,
            Permissions::CAN_CREATE_PAYMENT_CATEGORY,
            Permissions::CAN_CREATE_PAYMENT_ITEM,
            Permissions::CAN_CREATE_USER_PAYMENT,
            Permissions::CAN_CREATE_USER_SAVING,
            Permissions::CAN_CREATE_USER,
            Permissions::CAN_UPDATE_EXPENDITURE_CATEGORY,
            Permissions::CAN_UPDATE_EXPENDITURE_DETAIL,
            Permissions::CAN_UPDATE_EXPENDITURE_ITEM,
            Permissions::CAN_UPDATE_INCOME_ACTIVITY,
            Permissions::CAN_UPDATE_PAYMENT_CATEGORY,
            Permissions::CAN_UPDATE_PAYMENT_ITEM,
            Permissions::CAN_UPDATE_USER_PAYMENT,
            Permissions::CAN_UPDATE_USER_SAVING,
            Permissions::CAN_GET_EXPENDITURE_CATEGORIES,
            Permissions::CAN_GET_EXPENDITURE_CATEGORY,
            Permissions::CAN_GET_EXPENDITURE_DETAIL,
            Permissions::CAN_GET_EXPENDITURE_DETAILS,
            Permissions::CAN_GET_EXPENDITURE_CATEGORIES,
            Permissions::CAN_GET_EXPENDITURE_ITEM,
            Permissions::CAN_GET_EXPENDITURE_ITEMS,
            Permissions::CAN_GET_PAYMENT_CATEGORIES,
            Permissions::CAN_GET_PAYMENT_CATEGORY,
            Permissions::CAN_GET_PAYMENT_ITEM,
            Permissions::CAN_GET_PAYMENT_ITEMS,
            Permissions::CAN_GET_USER_PAYMENT,
            Permissions::CAN_GET_USER_PAYMENTS,
            Permissions::CAN_GET_USER_SAVING,
            Permissions::CAN_GET_USER_SAVINGS,
            Permissions::CAN_GET_INCOME_ACTIVITIES,
            Permissions::CAN_GET_INCOME_ACTIVITY,
            Permissions::CAN_GET_USERS,

        ]);

        $treasurerRole->givePermissionTo([
            Permissions::CAN_APPROVE_INCOME_FOR_EXPENDITURE_ITEM,
            Permissions::CAN_APPROVE_INCOME_FOR_INCOME_ACTIVITY,
            Permissions::CAN_APPROVE_USER_PAYMENT,
            Permissions::CAN_APPROVE_USER_SAVING,
            Permissions::CAN_GET_EXPENDITURE_CATEGORIES,
            Permissions::CAN_GET_EXPENDITURE_CATEGORY,
            Permissions::CAN_GET_EXPENDITURE_DETAIL,
            Permissions::CAN_GET_EXPENDITURE_DETAILS,
            Permissions::CAN_GET_EXPENDITURE_CATEGORIES,
            Permissions::CAN_GET_EXPENDITURE_ITEM,
            Permissions::CAN_GET_EXPENDITURE_ITEMS,
            Permissions::CAN_GET_PAYMENT_CATEGORIES,
            Permissions::CAN_GET_PAYMENT_CATEGORY,
            Permissions::CAN_GET_PAYMENT_ITEM,
            Permissions::CAN_GET_PAYMENT_ITEMS,
            Permissions::CAN_GET_USER_PAYMENT,
            Permissions::CAN_GET_USER_PAYMENTS,
            Permissions::CAN_GET_USER_SAVING,
            Permissions::CAN_GET_USER_SAVINGS,
            Permissions::CAN_GET_INCOME_ACTIVITIES,
            Permissions::CAN_GET_INCOME_ACTIVITY,
            Permissions::CAN_GET_USERS,
        ]);

        $auditorRole->givePermissionTo([
            Permissions::CAN_GET_USERS,
            Permissions::CAN_GET_USER_PAYMENTS,
            Permissions::CAN_GET_USER_SAVINGS,
            Permissions::CAN_GET_INCOME_ACTIVITIES
        ]);
    }
}
