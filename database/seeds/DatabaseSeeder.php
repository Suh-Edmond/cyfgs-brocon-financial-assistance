<?php

use Database\Seeders\SessionSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        $this->call(RoleAndPermissionSeeder::class);
//        $this->call(OrganisationSeeder::class);
//        $this->call(UserSeeder::class);
//        $this->call(ExpenditureCategorySeeder::class);
//        $this->call(ExpenditureItemSeeder::class);
//        $this->call(ExpenditureDetailSeeder::class);
//        $this->call(IncomeActivitySeeder::class);
//        $this->call(PaymentCategorySeeder::class);
//        $this->call(PaymentItemSeeder::class);
//        $this->call(UserContributionSeeder::class);
//        $this->call(UserSavingSeeder::class);
        $this->call(SessionSeeder::class);
    }
}
