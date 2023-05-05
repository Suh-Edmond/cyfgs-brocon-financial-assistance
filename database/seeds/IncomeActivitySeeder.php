<?php

use App\Models\IncomeActivity;
use App\Models\Organisation;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;

class IncomeActivitySeeder extends Seeder
{
    private $oragnisations;

    public function __construct()
    {
        $this->oragnisations = Organisation::all()->pluck('id');
    }


    public function run(Faker $faker)
    {
        for($i = 0; $i < 100; $i++)
        {
            IncomeActivity::create([
                'description'       => $faker->sentence,
                'amount'            => $faker->numberBetween(10000, 100000),
                'date'              => $faker->date(),
                'name'              => $faker->countryISOAlpha3(),
                'venue'             => $faker->sentence,
                'approve'           => $faker->randomElement(['PENDING', 'APPROVED', 'DECLINED']),
                'organisation_id'   => $this->oragnisations[0],
                'updated_by'        => $faker->name
            ]);
        }
    }
}
