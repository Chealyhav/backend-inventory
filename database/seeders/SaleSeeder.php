<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\User;
use Faker\Factory as Faker;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $customers = Customer::all();
        $users = User::all();

        if ($customers->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Skipping sale seeding: No customers or users found.');
            return;
        }

        for ($i = 1; $i <= 30; $i++) {
            Sale::create([
                'customer_id' => $customers->random()->id,
                'total_amount' => $faker->randomFloat(2, 100, 5000),
                'payment_method' => $faker->randomElement(['cash', 'card', 'bank_transfer']),
                'payment_status' => $faker->randomElement(['pending', 'paid', 'partially_paid']),
                'sale_date' => $faker->dateTimeBetween('-1 year', 'now'),
                'status' => true,
                'created_by' => $users->random()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('30 sample sales created successfully!');
    }
}
