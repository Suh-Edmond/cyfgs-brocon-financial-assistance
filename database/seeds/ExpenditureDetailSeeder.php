<?php

use App\Models\ExpenditureDetail;
use App\Models\ExpenditureItem;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class ExpenditureDetailSeeder extends Seeder
{
   private $expenditure_items;

   public function __construct()
   {
        $this->expenditure_items = ExpenditureItem::all()->count();
   }


    public function run(Faker $faker)
    {
        for($i = 0; $i < 500; $i++)
        {
            ExpenditureDetail::create([
                'name'                  => $faker->name,
                'amount_spent'          => $faker->numberBetween(5000, 50000),
                'amount_given'          => $faker->numberBetween(4000, 50000),
                'comment'               => $faker->sentence,
                'approve'               => $faker->randomElement([true, false]),
                'expenditure_item_id'   => $faker->randomElement([1, $this->expenditure_items])
            ]);
        }
    }
}
