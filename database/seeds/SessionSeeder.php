<?php

namespace Database\Seeders;

use App\Constants\SessionStatus;
use App\Models\Session;
use Illuminate\Database\Seeder;

class SessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Session::create([
            'year' =>  "2022",
            'status' => SessionStatus::IN_ACTIVE,
            'updated_by' => "Edmond"
        ]);
    }
}
