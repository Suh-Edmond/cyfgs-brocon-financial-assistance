<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;
use App\Models\Organisation;


class UserSeeder extends Seeder
{

    private $ogranisations;
    public function __construct()
    {
        $this->ogranisations = Organisation::all()->count();

    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        for($i = 0; $i < 100; $i++){
            DB::table('users')->insert([
                'name' => $faker->name,
                'email' => $faker->email(),
                'telephone' => $faker->phoneNumber(9),
                'password' => $faker->password(6),
                'address' => $faker->address(),
                'occupation' => $faker->sentence(5),
                'gender' => $faker->randomElement(['MALE', 'FEMALE']),
                'created_by' => $faker->name,
                'updated_by' => $faker->name,
                'organisation_id' =>  $faker->randomElement([1, $this->ogranisations])
            ]);
        }
    }
}
