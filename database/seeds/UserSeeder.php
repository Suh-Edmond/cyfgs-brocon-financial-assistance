<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;
use App\Models\Organisation;
use Ramsey\Uuid\Uuid;

class UserSeeder extends Seeder
{
    public $organisation;
    public function __construct()
    {
        $this->organisation = Organisation::latest()->first();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        DB::table('users')->insert([
            'name' => $faker->name,
            'email' => $faker->email(),
            'telephone' => $faker->phoneNumber(9),
            'password' => $faker->password(6),
            'address' => $faker->address(),
            'occupation' => $faker->sentence(5),
            'gender' => $faker->randomElement(['MALE', 'FEMALE']),
            'created_by' => $faker->name,
            'updated_by' => $faker->name,
            'organisation_id' =>  $this->organisation->id
        ]);
    }
}
