<?php

use App\Constants\PaymentStatus;
use App\Constants\SessionStatus;
use App\Models\ExpenditureCategory;
use App\Models\ExpenditureItem;
use App\Models\PaymentItem;
use App\Models\Session;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class ExpenditureItemSeeder extends Seeder
{

    private $expenditure_categories;
    private $paymentItems;
    private $session;

    public function __construct()
    {
        $this->session = Session::where('status', SessionStatus::ACTIVE)->first()->pluck('id');
        $this->expenditure_categories = ExpenditureCategory::all()->pluck('id');
        $this->paymentItems = PaymentItem::where('session', $this->session)->get()->pluck('id');
    }

    public function run(Faker $faker)
    {
        for($i = 0; $i < 100; $i++)
        {
            ExpenditureItem::create([
                'name'                      => $faker->name,
                'amount'                    => $faker->numberBetween(50000, 300000),
                'comment'                   => $faker->sentence,
                'approve'                   => PaymentStatus::APPROVED,
                'venue'                     => $faker->country,
                'expenditure_category_id'   => $faker->randomElement($this->expenditure_categories),
                'date'                      => $faker->date(),
                'updated_by'                => $faker->name,
                'session_id'                => $this->session,
                'payment_item_id'           => $faker->randomElement($this->paymentItems)
            ]);
        }
    }
}
