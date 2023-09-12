<?php

use App\Constants\Permissions;
use App\Constants\Roles;
use App\Constants\SessionStatus;
use App\Models\CustomPermission;
use App\Models\CustomRole;
use App\Models\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use \Illuminate\Support\Str;

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
            'name'       => Permissions::CAN_GET_USER_CONTRIBUTION,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);

        Permission::create([
            'name'       => Permissions::CAN_GET_USER_CONTRIBUTIONS,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);

        Permission::create([
            'name' => Permissions::CAN_ASSIGN_USER_ROLE,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);

        Permission::create([
            'name' => Permissions::CAN_CREATE_EXPENDITURE_CATEGORY,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);

        Permission::create([
            'name' => Permissions::CAN_CREATE_EXPENDITURE_DETAIL,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);

        Permission::create([
            'name' => Permissions::CAN_CREATE_EXPENDITURE_ITEM,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name' => Permissions::CAN_CREATE_INCOME_ACTIVITY,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name' => Permissions::CAN_CREATE_ORGANISATION,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name' => Permissions::CAN_CREATE_PAYMENT_CATEGORY,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name' => Permissions::CAN_CREATE_PAYMENT_ITEM,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name' => Permissions::CAN_CREATE_USER,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_DELETE_ORGANISATION,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_DELETE_ROLE,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'

        ]);
        Permission::create([
            'name'       => Permissions::CAN_CREATE_USER_PAYMENT,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_CREATE_USER_SAVING,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_DELETE_EXPENDITURE_CATEGORY,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_DELETE_EXPENDITURE_DETAIL,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_EXPENDITURE_ITEM,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_INCOME_ACTIVITY,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_PAYMENT_ITEM,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_USER,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_USER_PAYMENT,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name' => Permissions::CAN_DELETE_USER_SAVING,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_EXPENDITURE_CATEGORIES,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_EXPENDITURE_CATEGORY,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name' => Permissions::CAN_GET_EXPENDITURE_DETAIL,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_EXPENDITURE_DETAILS,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_EXPENDITURE_ITEM,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_EXPENDITURE_ITEMS,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_INCOME_ACTIVITIES,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_INCOME_ACTIVITY,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_ORGANISATION,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_PAYMENT_CATEGORIES,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_PAYMENT_CATEGORY,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_PAYMENT_ITEM,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_PAYMENT_ITEMS,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_USER,
            'guard_name' => 'api',
            'id'         =>Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_USER_PAYMENT,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_USER_PAYMENTS,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_USER_SAVING,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_USER_SAVINGS,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_GET_USERS,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_REMOVE_USER_ROLE,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_UPDATE_EXPENDITURE_CATEGORY,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
         Permission::create([
            'name'       => Permissions::CAN_UPDATE_EXPENDITURE_DETAIL,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
             'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_UPDATE_EXPENDITURE_ITEM,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_UPDATE_INCOME_ACTIVITY,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_UPDATE_ORGANISATION,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_UPDATE_PAYMENT_CATEGORY,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_UPDATE_USER,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_UPDATE_USER_PAYMENT,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name' => Permissions::CAN_UPDATE_USER_SAVING,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_DELETE_PAYMENT_CATEGORY,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
       Permission::create([
            'name'       => Permissions::CAN_UPDATE_PAYMENT_ITEM,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
           'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_APPROVE_USER_SAVING,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_APPROVE_INCOME_FOR_INCOME_ACTIVITY,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Permission::create([
            'name'       => Permissions::CAN_APPROVE_INCOME_FOR_EXPENDITURE_ITEM,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
       Permission::create([
                    'name'       => Permissions::CAN_APPROVE_USER_PAYMENT,
                    'guard_name' => 'api',
                    'id'         => Str::uuid()->toString(),
           'updated_by' => 'admin@admin.com'
        ]);


        Role::create([
            'name'       => Roles::ADMIN,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
        Role::create([
            'name'          => Roles::FINANCIAL_SECRETARY,
            'guard_name'    => 'api',
            'id'            => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
       Role::create([
            'name'       => Roles::TREASURER,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
           'updated_by' => 'admin@admin.com'
        ]);
        Role::create([
            'name'       => Roles::AUDITOR,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);
       Role::create([
            'name'       => Roles::PRESIDENT,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
           'updated_by' => 'admin@admin.com'
        ]);

       Role::create([
            'name'       => Roles::MEMBER,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
           'updated_by' => 'admin@admin.com'
        ]);

        Role::create([
            'name'       => Roles::SYSTEM_ADMIN,
            'guard_name' => 'api',
            'id'         => Str::uuid()->toString(),
            'updated_by' => 'admin@admin.com'
        ]);


        $role_admin     = CustomRole::findByName(Roles::ADMIN, 'api');
        $role_auditor   = CustomRole::findByName(Roles::AUDITOR, 'api');
        $role_president = CustomRole::findByName(Roles::PRESIDENT, 'api');
        $role_fin_sec   = CustomRole::findByName(Roles::FINANCIAL_SECRETARY, 'api');
        $role_treasurer = CustomRole::findByName(Roles::TREASURER, 'api');
        $system_admin = CustomRole::findByName(Roles::SYSTEM_ADMIN, 'api');


        $role_admin->givePermissionTo([
            CustomPermission::findByName(Permissions::CAN_DELETE_ORGANISATION, 'api'),
            CustomPermission::findByName(Permissions::CAN_DELETE_ROLE, 'api')
        ]);

        $role_president->givePermissionTo([
             CustomPermission::findByName(Permissions::CAN_CREATE_ORGANISATION, 'api'),
             CustomPermission::findByName(Permissions::CAN_GET_ORGANISATION, 'api'),
             CustomPermission::findByName(Permissions::CAN_UPDATE_ORGANISATION, 'api'),
             CustomPermission::findByName(Permissions::CAN_CREATE_USER, 'api'),
             CustomPermission::findByName(Permissions::CAN_ASSIGN_USER_ROLE, 'api'),
             CustomPermission::findByName(Permissions::CAN_REMOVE_USER_ROLE, 'api'),
             CustomPermission::findByName(Permissions::CAN_CREATE_INCOME_ACTIVITY, 'api'),
             CustomPermission::findByName(Permissions::CAN_GET_USER_SAVINGS, 'api'),
             CustomPermission::findByName(Permissions::CAN_CREATE_PAYMENT_CATEGORY, 'api'),
             CustomPermission::findByName(Permissions::CAN_CREATE_EXPENDITURE_CATEGORY, 'api'),
             CustomPermission::findByName(Permissions::CAN_UPDATE_USER, 'api'),
             CustomPermission::findByName(Permissions::CAN_DELETE_USER, 'api'),
             CustomPermission::findByName(Permissions::CAN_GET_PAYMENT_CATEGORIES, 'api'),
             CustomPermission::findByName(Permissions::CAN_GET_PAYMENT_CATEGORY, 'api'),
             CustomPermission::findByName(Permissions::CAN_UPDATE_PAYMENT_CATEGORY, 'api'),
             CustomPermission::findByName(Permissions::CAN_DELETE_EXPENDITURE_CATEGORY, 'api'),
             CustomPermission::findByName(Permissions::CAN_DELETE_EXPENDITURE_DETAIL, 'api'),
             CustomPermission::findByName(Permissions::CAN_DELETE_EXPENDITURE_ITEM, 'api'),
             CustomPermission::findByName(Permissions::CAN_DELETE_INCOME_ACTIVITY, 'api'),
             CustomPermission::findByName(Permissions::CAN_DELETE_PAYMENT_CATEGORY, 'api'),
             CustomPermission::findByName(Permissions::CAN_DELETE_PAYMENT_ITEM, 'api'),
             CustomPermission::findByName(Permissions::CAN_DELETE_USER_PAYMENT, 'api'),
             CustomPermission::findByName(Permissions::CAN_DELETE_USER_SAVING, 'api'),
        ]);

        $role_fin_sec->givePermissionTo([
            CustomPermission::findByName(Permissions::CAN_GET_USER_CONTRIBUTIONS, 'api'),
            CustomPermission::findByName(Permissions::CAN_GET_USER_CONTRIBUTION, 'api'),
            CustomPermission::findByName(Permissions::CAN_CREATE_EXPENDITURE_CATEGORY, 'api'),
            CustomPermission::findByName(Permissions::CAN_CREATE_EXPENDITURE_DETAIL, 'api'),
            CustomPermission::findByName(Permissions::CAN_CREATE_EXPENDITURE_ITEM, 'api'),
            CustomPermission::findByName(Permissions::CAN_CREATE_INCOME_ACTIVITY ,'api'),
            CustomPermission::findByName(Permissions::CAN_CREATE_PAYMENT_CATEGORY ,'api'),
            CustomPermission::findByName(Permissions::CAN_CREATE_PAYMENT_ITEM ,'api'),
            CustomPermission::findByName(Permissions::CAN_CREATE_USER_PAYMENT ,'api'),
            CustomPermission::findByName(Permissions::CAN_CREATE_USER_SAVING ,'api'),
            CustomPermission::findByName(Permissions::CAN_CREATE_USER ,'api'),
            CustomPermission::findByName(Permissions::CAN_UPDATE_EXPENDITURE_CATEGORY ,'api'),
            CustomPermission::findByName(Permissions::CAN_UPDATE_EXPENDITURE_DETAIL ,'api'),
            CustomPermission::findByName(Permissions::CAN_UPDATE_EXPENDITURE_ITEM ,'api'),
            CustomPermission::findByName(Permissions::CAN_UPDATE_INCOME_ACTIVITY ,'api'),
            CustomPermission::findByName(Permissions::CAN_UPDATE_PAYMENT_CATEGORY ,'api'),
            CustomPermission::findByName(Permissions::CAN_UPDATE_PAYMENT_ITEM ,'api'),
            CustomPermission::findByName(Permissions::CAN_UPDATE_USER_PAYMENT ,'api'),
            CustomPermission::findByName(Permissions::CAN_UPDATE_USER_SAVING ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_EXPENDITURE_CATEGORIES ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_EXPENDITURE_CATEGORY ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_EXPENDITURE_DETAIL ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_EXPENDITURE_DETAILS ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_EXPENDITURE_ITEM ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_EXPENDITURE_ITEMS ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_PAYMENT_CATEGORIES ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_PAYMENT_CATEGORY ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_PAYMENT_ITEM ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_PAYMENT_ITEMS ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_USER_PAYMENT ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_USER_PAYMENTS ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_USER_SAVING ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_USER_SAVINGS ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_INCOME_ACTIVITIES ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_INCOME_ACTIVITY ,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_USERS ,'api'),

        ]);

        $role_treasurer->givePermissionTo([
            CustomPermission::findByName(Permissions::CAN_GET_USER_CONTRIBUTIONS,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_USER_CONTRIBUTION,'api'),
            CustomPermission::findByName(Permissions::CAN_APPROVE_INCOME_FOR_EXPENDITURE_ITEM,'api'),
            CustomPermission::findByName(Permissions::CAN_APPROVE_INCOME_FOR_INCOME_ACTIVITY,'api'),
            CustomPermission::findByName(Permissions::CAN_APPROVE_USER_PAYMENT,'api'),
            CustomPermission::findByName(Permissions::CAN_APPROVE_USER_SAVING,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_EXPENDITURE_CATEGORIES,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_EXPENDITURE_CATEGORY,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_EXPENDITURE_DETAIL,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_EXPENDITURE_DETAILS,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_EXPENDITURE_ITEM,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_EXPENDITURE_ITEMS,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_PAYMENT_CATEGORIES,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_PAYMENT_CATEGORY,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_PAYMENT_ITEM,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_PAYMENT_ITEMS,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_USER_PAYMENT,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_USER_PAYMENTS,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_USER_SAVING,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_USER_SAVINGS,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_INCOME_ACTIVITIES,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_INCOME_ACTIVITY,'api'),
            CustomPermission::findByName(Permissions::CAN_GET_USERS,'api'),
        ]);

        $role_auditor->givePermissionTo([
           CustomPermission::findByName(Permissions::CAN_GET_USERS,'api'),
           CustomPermission::findByName(Permissions::CAN_GET_USER_PAYMENTS,'api'),
           CustomPermission::findByName(Permissions::CAN_GET_USER_SAVINGS,'api'),
           CustomPermission::findByName(Permissions::CAN_GET_INCOME_ACTIVITIES,'api'),
        ]);
    }
}
