<?php

use App\Constants\Permissions;
use App\Constants\Roles;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'name'      => Permissions::CAN_GET_USER_CONTRIBUTION,
            'guard_name' => 'api'
        ]);

        Permission::create([
            'name'      => Permissions::CAN_GET_USER_CONTRIBUTIONS,
            'guard_name' => 'api'
        ]);

        Permission::create([
            'name' => Permissions::CAN_ASSIGN_USER_ROLE,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_CREATE_EXPENDITURE_CATEGORY,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_CREATE_EXPENDITURE_DETAIL,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_CREATE_EXPENDITURE_ITEM,
            'guard_name' => 'api']);
        Permission::create([
            'name' => Permissions::CAN_CREATE_INCOME_ACTIVITY,
            'guard_name' => 'api']);
        Permission::create([
            'name' => Permissions::CAN_CREATE_ORGANISATION,
            'guard_name' => 'api']);
        Permission::create([
            'name' => Permissions::CAN_CREATE_PAYMENT_CATEGORY,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_CREATE_PAYMENT_ITEM,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_CREATE_USER,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_ORGANISATION,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_ROLE,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_CREATE_USER_PAYMENT,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_CREATE_USER_SAVING,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_EXPENDITURE_CATEGORY,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_EXPENDITURE_DETAIL,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_EXPENDITURE_ITEM,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_INCOME_ACTIVITY,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_PAYMENT_ITEM,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_USER,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_USER_PAYMENT,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_USER_SAVING,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_EXPENDITURE_CATEGORIES,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_EXPENDITURE_CATEGORY,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_EXPENDITURE_DETAIL,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_EXPENDITURE_DETAILS,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_EXPENDITURE_ITEM,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_EXPENDITURE_ITEMS,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_INCOME_ACTIVITIES,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_INCOME_ACTIVITY,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_ORGANISATION,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_PAYMENT_CATEGORIES,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_PAYMENT_CATEGORY,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_PAYMENT_ITEM,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_PAYMENT_ITEMS,
            'guard_name' => 'api']);
        Permission::create([
            'name' => Permissions::CAN_GET_USER,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_USER_PAYMENT,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_USER_PAYMENTS,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_USER_SAVING,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_USER_SAVINGS,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_USERS,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_REMOVE_USER_ROLE,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_UPDATE_EXPENDITURE_CATEGORY,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_UPDATE_EXPENDITURE_DETAIL,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_UPDATE_EXPENDITURE_ITEM,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_UPDATE_INCOME_ACTIVITY,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_UPDATE_ORGANISATION,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_UPDATE_PAYMENT_CATEGORY,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_UPDATE_USER,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_UPDATE_USER_PAYMENT,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_UPDATE_USER_SAVING,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_PAYMENT_CATEGORY,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_UPDATE_PAYMENT_ITEM,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_APPROVE_USER_SAVING,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_APPROVE_INCOME_FOR_INCOME_ACTIVITY,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_APPROVE_INCOME_FOR_EXPENDITURE_ITEM,
            'guard_name' => 'api'
        ]);
        Permission::create([
            'name' => Permissions::CAN_APPROVE_USER_PAYMENT,
            'guard_name' => 'api'
        ]);


        $adminRole = Role::create([
            'name' => Roles::ADMIN,
            'guard_name' => 'api'
        ]);
        $financialSecretaryRole = Role::create([
            'name' => Roles::FINANCIAL_SECRETARY,
            'guard_name' => 'api'
        ]);
        $treasurerRole = Role::create([
            'name' => Roles::TREASURER,
            'guard_name' => 'api'
        ]);
        $auditorRole = Role::create([
            'name' => Roles::AUDITOR,
            'guard_name' => 'api'
        ]);
        $presidentRole = Role::create([
            'name' => Roles::PRESIDENT,
            'guard_name' => 'api'
        ]);

        $user = Role::create([
            'name' => Roles::USER,
            'guard_name' => 'api'
        ]);


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
            Permissions::CAN_GET_PAYMENT_CATEGORIES,
            Permissions::CAN_GET_PAYMENT_CATEGORY,
            Permissions::CAN_UPDATE_PAYMENT_CATEGORY,
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
            Permissions::CAN_GET_USER_CONTRIBUTIONS,
            Permissions::CAN_GET_USER_CONTRIBUTION,
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
            Permissions::CAN_GET_USER_CONTRIBUTIONS,
            Permissions::CAN_GET_USER_CONTRIBUTION,
            Permissions::CAN_APPROVE_INCOME_FOR_EXPENDITURE_ITEM,
            Permissions::CAN_APPROVE_INCOME_FOR_INCOME_ACTIVITY,
            Permissions::CAN_APPROVE_USER_PAYMENT,
            Permissions::CAN_APPROVE_USER_SAVING,
            Permissions::CAN_GET_EXPENDITURE_CATEGORIES,
            Permissions::CAN_GET_EXPENDITURE_CATEGORY,
            Permissions::CAN_GET_EXPENDITURE_DETAIL,
            Permissions::CAN_GET_EXPENDITURE_DETAILS,
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
