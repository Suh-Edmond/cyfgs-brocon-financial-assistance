<?php

use App\Models\Organisation;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class OrganisationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        for($i = 0; $i < 5; $i++)
        {
            Organisation::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'telephone' => $faker->phoneNumber,
                'address' => $faker->address,
                'description' => $faker->sentence,
                'logo' => $faker->name,
                'salutation' => $faker->sentence
            ]);
        }

    }
}
