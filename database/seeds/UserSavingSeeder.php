<?php

use App\Models\User;
use App\Models\UserSaving;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class UserSavingSeeder extends Seeder
{
    private $users;
    private $session;

    public function __construct()
    {
        $this->users = User::all()->pluck('id');
        $this->session       = \App\Models\Session::where('status', \App\Constants\SessionStatus::ACTIVE)->first();
    }


    public function run(Faker $faker)
    {
        for($i = 0; $i < 500; $i++)
        {
            UserSaving::create([
                'amount_deposited'  => $faker->numberBetween(1000, 100000),
                'approve'           => \App\Constants\PaymentStatus::APPROVED,
                'comment'           => $faker->sentence,
                'user_id'           => $faker->randomElement($this->users),
                'updated_by'        => $faker->name,
                'session_id'        => $this->session->id,
                'amount_used'       => 1000
            ]);
        }
    }
}
