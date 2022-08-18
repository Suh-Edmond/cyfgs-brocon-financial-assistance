<?php

use App\Models\ExpenditureCategory;
use App\Models\ExpenditureItem;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class ExpenditureItemSeeder extends Seeder
{

    private $expenditure_categories;

    public function __construct()
    {
        $this->expenditure_categories = ExpenditureCategory::all()->count();
    }

    public function run(Faker $faker)
    {
        for($i = 0; $i < 100; $i++)
        {
            ExpenditureItem::create([
                'name'                      => $faker->name,
                'amount'                    => $faker->numberBetween(50000, 300000),
                'comment'                   => $faker->sentence,
                'approve'                   => $faker->randomElement([true, false]),
                'venue'                     => $faker->country,
                'expenditure_category_id'   => rand(1, $this->expenditure_categories),
                'date'                      => $faker->date()
            ]);
        }
    }
}
