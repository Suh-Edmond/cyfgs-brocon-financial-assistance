<?php

use App\Models\Organisation;
use App\Models\PaymentCategory;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class PaymentCategorySeeder extends Seeder
{
    private $organisations;

    public function __construct()
    {
        $this->organisations = Organisation::all()->pluck('id');
    }


    public function run(Faker $faker)
    {
       for($i = 0; $i < 50; $i++)
       {
            PaymentCategory::create([
                'name'              => $faker->name,
                'description'       => $faker->sentence,
                'organisation_id'   => $this->organisations[0],
                'updated_by'        => $faker->name
            ]);
       }
    }
}
