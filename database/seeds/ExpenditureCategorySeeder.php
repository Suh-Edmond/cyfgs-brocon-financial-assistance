<?php

use App\Models\ExpenditureCategory;
use App\Models\Organisation;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class ExpenditureCategorySeeder extends Seeder
{

    private $organisations;

    public function __construct()
    {
        $this->organisations = Organisation::all()->count();
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        for($i = 0; $i < 20; $i++)
        {
            ExpenditureCategory::create([
                'name'             => $faker->colorName,
                'description'       => $faker->sentence,
                'organisation_id'   => $faker->randomElement([1, $this->organisations])
            ]);
        }
    }
}
