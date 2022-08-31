<?php

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

    public function __construct()
    {
        $this->payment_items = PaymentItem::all()->pluck('id');
        $this->users         = User::all()->pluck('id');
    }


    public function run(Faker $faker)
    {
        for($i = 0; $i < 1000; $i++)
        {
            UserContribution::create([
                'code'                  => Uuid::uuid4(),
                'comment'               => $faker->sentence,
                'amount_deposited'      => $faker->randomElement([500, 30000]),
                'approve'               => $faker->randomElement([true, false]),
                'user_id'               => $faker->randomElement($this->users),
                'payment_item_id'       => $faker->randomElement($this->payment_items),
                'status'                => $faker->randomElement(['COMPLETE', 'INCOMEPLETE'])
            ]);
        }
    }

}
