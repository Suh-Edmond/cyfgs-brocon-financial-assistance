<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;
use App\Models\Organisation;

class UserSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        DB::table('users')->insert([
            'id' => "4929bb93-e16e-41c8-a4bb-0724fccd352d",
            'name' => $faker->name,
            'email' => $faker->email(),
            'telephone' => $faker->phoneNumber(9),
            'password' => $faker->password(6),
            'address' => $faker->address(),
            'occupation' => $faker->sentence(5),
            'gender' => $faker->randomElement(['MALE', 'FEMALE']),
            'created_by' => $faker->name,
            'updated_by' => $faker->name,
            'organisation_id' =>  "1ed5ddb4-b0f9-4cea-a3bf-7849b27f4302"
        ]);
    }
}
