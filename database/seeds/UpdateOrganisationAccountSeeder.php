<?php
namespace Database\Seeders;

use App\Models\Organisation;
use Illuminate\Database\Seeder;

class UpdateOrganisationAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Organisation::all()->each(function ($organisation) {
            $subscription = $organisation->subscriptions()->first() ?? null;
            if ($subscription) {
                $subscription->update([
                    'status' => 'active',
                ]);
            }
        });
    }
}
