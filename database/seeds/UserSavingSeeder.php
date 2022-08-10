<?php

use App\Models\User;
use App\Models\UserSaving;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class UserSavingSeeder extends Seeder
{
    private $users;

    public function __construct()
    {
        $this->users = User::all()->count();
    }


    public function run(Faker $faker)
    {
        for($i = 0; $i < 500; $i++)
        {
            UserSaving::create([
                'name'              => $faker->name,
                'amount_deposited'  => $faker->numberBetween(1000, 100000),
                'approve'           => $faker->randomElement([true, false]),
                'comment'           => $faker->sentence,
                'user_id'           => $faker->randomElement([1, $this->users])
            ]);
        }
    }
}
