<?php

namespace Database\Seeders;

use App\Models\Registration;
use Illuminate\Database\Seeder;

class RegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Registration::create([
            'amount' => 500,
            'status'  => 'ACTIVE',
            'updated_by' => 'default',
            'is_compulsory' => true,
            'frequency'  => 'YEARLY'
        ]);
    }
}
