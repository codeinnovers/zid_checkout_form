<?php

namespace Database\Seeders;

use App\Models\ZidMerchant;
use Illuminate\Database\Seeder;

class ZidMerchantSeeder extends Seeder
{
    public function run(): void
    {
        ZidMerchant::factory()->count(1)->create();
    }
}
