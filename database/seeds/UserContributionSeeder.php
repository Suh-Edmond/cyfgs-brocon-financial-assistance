<?php

use App\Constants\PaymentStatus;
use App\Models\PaymentItem;
use App\Models\User;
use App\Models\UserContribution;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use Ramsey\Uuid\Uuid;

class UserContributionSeeder extends Seeder
{
    private $payment_items;
    private $users;
    private $session;

    public function __construct()
    {
        $this->payment_items = PaymentItem::all();
        $this->users         = User::all()->pluck('id');
        $this->session       = \App\Models\Session::where('status', \App\Constants\SessionStatus::ACTIVE)->first()->pluck('id');
    }


    public function run(Faker $faker)
    {
        for($i = 0; $i < 1000; $i++)
        {
            $user = $faker->randomElement($this->users);
            $payment_item = $faker->randomElement($this->payment_items);
            $usersContributions = UserContribution::where('session_id', $this->session)->where('payment_item_id', $payment_item->id)->get();
            $totalAmountContributed = collect($usersContributions)->sum('amount_deposited');
            if($payment_item->amount != $totalAmountContributed){
                UserContribution::create([
                    'code'                  => Uuid::uuid4(),
                    'comment'               => $faker->sentence,
                    'amount_deposited'      => $faker->randomElement([1000, 30000]),
                    'approve'               => PaymentStatus::APPROVED,
                    'user_id'               => $user->id,
                    'payment_item_id'       => $payment_item->id,
                    'status'                => $faker->randomElement(['COMPLETE', 'INCOMPLETE']),
                    'updated_by'            => $faker->name,
                    'month_name'            => '',
                    'quarter_name'          => '',
                    'session_id'            => $this->session
                ]);
            }else{
                UserContribution::create([
                    'code'                  => Uuid::uuid4(),
                    'comment'               => $faker->sentence,
                    'amount_deposited'      => $faker->randomElement([1000, 30000]),
                    'approve'               => PaymentStatus::APPROVED,
                    'user_id'               => $user->id,
                    'payment_item_id'       => $payment_item->id,
                    'status'                => $faker->randomElement(['COMPLETE', 'INCOMPLETE']),
                    'updated_by'            => $faker->name,
                    'month_name'            => '',
                    'quarter_name'          => '',
                    'session_id'            => $this->session
                ]);
            }

        }
    }

}
