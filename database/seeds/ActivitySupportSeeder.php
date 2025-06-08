<?php

namespace Database\Seeders;

use App\Constants\PaymentStatus;
use App\Constants\SessionStatus;
use App\Models\ActivitySupport;
use App\Models\PaymentItem;
use App\Models\Session;
use Faker\Generator;
use Illuminate\Database\Seeder;

class ActivitySupportSeeder extends Seeder
{
    private $paymentItems;
    private $session;

    public function __construct()
    {
        $this->session = Session::where('status', SessionStatus::ACTIVE)->first();
        $this->paymentItems = PaymentItem::where('session_id', $this->session->id)->get()->pluck('id');
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $generator)
    {
        for ($i = 0; $i < 100; $i++) {
            ActivitySupport::create([
                'amount_deposited' => $generator->randomElement([10000, 15000, 50000, 20000, 40000, 30000]),
                'comment' => $generator->sentence(10),
                'supporter' => $generator->name,
                'payment_item_id' => $generator->randomElement($this->paymentItems),
                'code' => $generator->randomNumber(5 ),
                'scan_picture' => '',
                'updated_by' => "default",
                'approve' => PaymentStatus::APPROVED,
                'session_id'  => $this->session->id
            ]);
        }
    }
}
