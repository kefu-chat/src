<?php

namespace Database\Seeders;

use App\Models\Enterprise;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionSeeder::class);
        $this->call(InstitutionsTableSeeder::class);
        $this->call(PlansTableSeeder::class);
        $this->call(AdminSeeder::class);
    }
}
