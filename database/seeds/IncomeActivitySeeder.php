<?php

use App\Constants\PaymentStatus;
use App\Constants\SessionStatus;
use App\Models\IncomeActivity;
use App\Models\Organisation;
use App\Models\PaymentItem;
use App\Models\Session;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;

class IncomeActivitySeeder extends Seeder
{
    private $oragnisations;
    private $session;
    private $paymentItem;

    public function __construct()
    {
        $this->oragnisations = Organisation::all()->pluck('id');
        $this->session = Session::where('status', SessionStatus::ACTIVE)->first();
        $this->paymentItem = PaymentItem::where('session_id', $this->session->id)->get()->pluck('id');
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
                'approve'           => PaymentStatus::APPROVED,
                'organisation_id'   => $this->oragnisations[0],
                'updated_by'        => $faker->name,
                'session_id'        => $this->session->id,
                'payment_item_id'   => $faker->randomElement($this->paymentItem)
            ]);
        }
    }
}
