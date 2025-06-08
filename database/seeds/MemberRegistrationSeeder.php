<?php

namespace Database\Seeders;

use App\Constants\PaymentStatus;
use App\Constants\SessionStatus;
use App\Models\MemberRegistration;
use App\Models\Registration;
use App\Models\Session;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;

class MemberRegistrationSeeder extends Seeder
{
    private $registrations;
    private $users;

    private $session;
    public function __construct()
    {
        $this->registrations = Registration::where('status', SessionStatus::ACTIVE)->first()->pluck('id');
        $this->users = User::all()->pluck('id');
        $this->session = Session::where('status', SessionStatus::ACTIVE)->first()->pluck('id');

    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        for ($i = 0; $i < 300; $i++) {
            MemberRegistration::create([
                'approve' => PaymentStatus::APPROVED,
                'user_id'  => $faker->randomElement($this->users),
                'updated_by' => 'default',
                'session_id' => $faker->randomElement($this->session),
                'month_name' => 'default',
                'registration_id' => $faker->randomElement($this->registrations)
            ]);
        }
    }
}
