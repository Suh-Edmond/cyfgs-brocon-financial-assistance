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
//        Session::create([
//            'year' =>  "2027",
//            'status' => SessionStatus::IN_ACTIVE,
//            'updated_by' => "Edmond"
//        ]);
//        Session::create([
//            'year' =>  "2026",
//            'status' => SessionStatus::IN_ACTIVE,
//            'updated_by' => "Edmond"
//        ]);
//        Session::create([
//            'year' =>  "2025",
//            'status' => SessionStatus::IN_ACTIVE,
//            'updated_by' => "Edmond"
//        ]);
//        Session::create([
//            'year' =>  "2024",
//            'status' => SessionStatus::IN_ACTIVE,
//            'updated_by' => "Edmond"
//        ]);
        Session::create([
            'year' =>  "2024",
            'status' => SessionStatus::ACTIVE,
            'updated_by' => "Edmond"
        ]);
    }
}
