<?php

use App\Models\Organisation;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

class OrganisationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        Organisation::create([
            'id'            => Str::uuid()->toString(),
            'name'          => "Brothers Corner Great Soppo (BROCON)",
            'email'         => "brocon@gmail.com",
            'region'        => "Southwest",
            'telephone'     => "+237683404289",
            'address'       => "Buea",
            'description'   => "The Brothers Corner of the Christian Youth Fellowship Movement of the Presbyterian Church in Cameroon, in Great Soppo",
            'logo'          => "",
            'salutation'    => "Brothers we lead the way. In Christ we Head the Trail",
            'box_number'    => 1523,
            'updated_by'    => "Admin"
        ]);

    }
}
