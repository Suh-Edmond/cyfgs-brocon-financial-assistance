<?php

use App\Constants\Roles;
use App\Models\CustomRole;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Models\Organisation;
use App\Models\User;
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
        for($i = 0; $i < 100; $i++){
            $saved = User::create([
                    'name'            => $faker->name,
                    'email'           => $faker->email(),
                    'telephone'       => $faker->phoneNumber,
                    'password'        => $faker->password(6),
                    'address'         => $faker->address(),
                    'occupation'      => $faker->sentence(5),
                    'gender'          => $faker->randomElement(['MALE', 'FEMALE']),
                    'organisation_id' => $this->organisation[0],
                    'updated_by'      => $faker->name
                ]);
            $saved->assignRole($this->role_user);
        }

        $system_user = User::create([
            'name'              => "Suh Edmond",
            'email'             => 'email@gmail.com',
            'telephone'         => '671809232',
            'password'          => Hash::make('password'),
            'address'           => $faker->address(),
            'occupation'        => $faker->sentence(5),
            'gender'            => $faker->randomElement(['MALE', 'FEMALE']),
            'organisation_id'   => $this->organisation[0],
            'updated_by'        => $faker->name
        ]);

        $role_admin     = CustomRole::findByName(Roles::ADMIN, 'api');
        $role_auditor   = CustomRole::findByName(Roles::AUDITOR, 'api');
        $role_president = CustomRole::findByName(Roles::PRESIDENT, 'api');
        $role_fin_sec   = CustomRole::findByName(Roles::FINANCIAL_SECRETARY, 'api');
        $role_treasurer = CustomRole::findByName(Roles::TREASURER, 'api');


        $system_user->assignRole($this->role_user);
        // $system_user->assignRole($role_admin);
        // $system_user->assignRole($role_auditor);
        $system_user->assignRole($role_president);
        // $system_user->assignRole($role_fin_sec);
        // $system_user->assignRole($role_treasurer);

    }
}
