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

    private $organisations;
    private $role_user;

    public function __construct()
    {
        $this->organisations = Organisation::all()->pluck('id');
        $this->role_user     = CustomRole::findByName(Roles::USER, 'api');

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
                    'name' => $faker->name,
                    'email' => $faker->email(),
                    'telephone' => $faker->phoneNumber(9),
                    'password' => $faker->password(6),
                    'address' => $faker->address(),
                    'occupation' => $faker->sentence(5),
                    'gender' => $faker->randomElement(['MALE', 'FEMALE']),
                    'organisation_id' => $this->organisations[0]
                ]);
            $saved->assignRole($this->role_user);
        }

        $saved = User::create([
            'name' => "Suh Edmond",
            'email' => 'email@gmail.com',
            'telephone' => '671809232',
            'password' => Hash::make('password'),
            'address' => $faker->address(),
            'occupation' => $faker->sentence(5),
            'gender' => $faker->randomElement(['MALE', 'FEMALE']),
            'organisation_id' => $this->organisations[0]
        ]);

        $role_admin     = CustomRole::findByName(Roles::ADMIN, 'api');
        $role_auditor   = CustomRole::findByName(Roles::AUDITOR, 'api');
        $role_president = CustomRole::findByName(Roles::PRESIDENT, 'api');
        $role_fin_sec   = CustomRole::findByName(Roles::FINANCIAL_SECRETARY, 'api');
        $role_treasurer = CustomRole::findByName(Roles::TREASURER, 'api');


        $saved->assignRole($this->role_user);
        $saved->assignRole($role_admin);
        $saved->assignRole($role_auditor);
        $saved->assignRole($role_president);
        $saved->assignRole($role_fin_sec);
        $saved->assignRole($role_treasurer);

    }
}
