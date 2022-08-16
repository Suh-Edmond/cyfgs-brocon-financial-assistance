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
        Organisation::create([
            'name'          => $faker->name,
            'email'         => $faker->email,
            'region'        => $faker->country,
            'telephone'     => $faker->phoneNumber,
            'address'       => $faker->address,
            'description'   => $faker->sentence,
            'logo'          => $faker->name,
            'salutation'    => $faker->sentence,
            'box_number'    => $faker->randomDigit
        ]);

    }
}
