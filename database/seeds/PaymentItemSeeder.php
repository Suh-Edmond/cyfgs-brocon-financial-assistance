<?php

use App\Models\PaymentCategory;
use App\Models\PaymentItem;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class PaymentItemSeeder extends Seeder
{
    private $payment_categories;

    public function __construct()
    {
        $this->payment_categories = PaymentCategory::all()->count();
    }

    public function run(Faker $faker)
    {
        for($i = 0; $i < 400; $i++)
        {
            PaymentItem::create([
                'name'          => $faker->name,
                'amount'        => $faker->numberBetween(5000, 100000),
                'complusory'    => $faker->randomElement([true, false]),
                'payment_category_id'   => $faker->randomElement([1, $this->payment_categories])
            ]);
        }
    }
}
