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
        $this->oragnisations = Organisation::all()->count();
    }


    public function run(Faker $faker)
    {
        for($i = 0; $i < 100; $i++)
        {
            IncomeActivity::create([
                'description'       => $faker->sentence,
                'amount'            => $faker->numberBetween(10000, 100000),
                'date'              => $faker->date(),
                'venue'             => $faker->sentence,
                'approve'           => $faker->randomElement([true, false]),
                'organisation_id'   => rand(1, $this->oragnisations)
            ]);
        }
    }
}
