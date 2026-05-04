<?php

use App\Constants\Roles;
use App\Constants\SessionStatus;
use App\Models\CustomRole;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    private $organisation;
    private $role_user;

    public function __construct()
    {
        $this->organisation  = Organisation::all()->pluck('id');
        $this->role_user     = CustomRole::findByName(Roles::MEMBER, 'api');
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        // $admin =  User::create([
        //     'name'            => "Suh Edmond Neba",
        //     'email'           => "suhedmond25@yahoo.com",
        //     'telephone'       => "+2376736622071",
        //     'gender'          => "MALE",
        //     'address'         => "Buea, Cameroon",
        //     'occupation'      => 'Software Engineer',
        //     'organisation_id' => $this->organisation[0],
        //     'updated_by'      => "Edmond",
        //     'status'          => SessionStatus::ACTIVE,
        //     'password'        => Hash::make("Summer123!"),
        //     "email_verified_at" => Carbon::now(),
        //     'username'        => "suhedmond25"
        // ]);

        // $admin_role = CustomRole::findByName(Roles::MEMBER, 'api');
        // $admin_role2 = CustomRole::findByName(Roles::ADMIN, 'api');
        // $this->saveUserRole($admin, $admin_role,  "Admin");
        // $this->saveUserRole($admin, $admin_role2,  "Admin");


        // $sys_admin =  User::create([
        //     'name'            => "Test User1",
        //     'email'           => "testuser1@gmail.com",
        //     'telephone'       => "+237674667771",
        //     'gender'          => "MALE",
        //     'address'         => "Buea, Cameroon",
        //     'occupation'      => 'Software Engineer',
        //     'organisation_id' => $this->organisation[0],
        //     'updated_by'      => "Edmond",
        //     'status'          => SessionStatus::ACTIVE,
        //     'password'        => Hash::make("Summer123!"),
        //     "email_verified_at" => Carbon::now(),
        //     'username'          => "testuser1"

        // ]);
        $organisation = Organisation::where('email', 'testuserorganisation@gmail.com')->first();

        $user = User::where('username', "testuser")->first();
        $user->update([
            'email_verified_at' => Carbon::now()
        ]);

        // $sys_admin_role = CustomRole::findByName(Roles::MEMBER, 'api');
        $sys_admin = CustomRole::findByName(Roles::SYSTEM_ADMIN, 'api');
        $this->saveUserRole($user, $sys_admin,  "SYSTEM_ADMIN", $organisation->id);

        // $this->saveUserRole($sys_admin, $sys_admin_role2,  "Admin");



    //    for($i = 0; $i < 90; $i++){
    //        $created =  User::create([
    //            'name'            => $faker->name,
    //            'email'           => $faker->email,
    //            'telephone'       => $faker->phoneNumber,
    //            'gender'          => $faker->randomElement(['MALE', 'FEMALE']),
    //            'address'         => $faker->address,
    //            'occupation'      => $faker->randomElement(['Teacher', 'Accountant', 'Software Engineer', 'Banker', 'Devops Engineer', 'Nurse', 'Electrician', 'Plumber']),
    //            'organisation_id' => $this->organisation[0],
    //            'updated_by'      => "James Mark",
    //            'status'          => SessionStatus::ACTIVE
    //        ]);

    //        $role = CustomRole::findByName(Roles::MEMBER, 'api');
    //        $this->saveUserRole($created, $role,  "James Mark");
    //    }
    }

    public function  saveUserRole($user, $role, $updated_by, $organisation_id = null)
    {
        DB::table('model_has_roles')->insert([
            'role_id'       => $role->id,
            'model_id'      => $user->id,
            'model_type'    => 'App\Models\User',
            'updated_by'    => $updated_by,
            'organisation_id' => $organisation_id
        ]);
    }
}
