<?php

use App\Models\PaymentCategory;
use App\Models\PaymentItem;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class PaymentItemSeeder extends Seeder
{
    private $payment_categories;
    private $session;

    public function __construct()
    {
        $this->payment_categories = PaymentCategory::all()->pluck('id');
        $this->session = \App\Models\Session::where('status', \App\Constants\SessionStatus::ACTIVE)->first();
    }

    public function run(Faker $faker)
    {
        for($i = 0; $i < 100; $i++)
        {
            PaymentItem::create([
                'name'                  => $faker->name,
                'amount'                => $faker->numberBetween(5000, 100000),
                'compulsory'            => $faker->randomElement([true, false]),
                'payment_category_id'   => $faker->randomElement($this->payment_categories),
                'updated_by'            => $faker->name,
                'type'                 => \App\Constants\PaymentItemType::ALL_MEMBERS,
                'frequency'             => \App\Constants\PaymentItemFrequency::ONE_TIME,
                'session_id'            => $this->session->id,
                'reference'             => "",
                'deadline'              => \Carbon\Carbon::now()->addMonths(3),
                'is_range'              => false,
                'start_amount'          => 0,
                'end_amount'            => 0
            ]);
        }
    }
}
