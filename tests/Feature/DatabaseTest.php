<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    /**
     * 测试表结构构建
     *
     * @return void
     */
    public function testMigrates()
    {
        $this->artisan('migrate')->assertExitCode(0);
        ob_clean();
    }

    /**
     * 测试表种子数据
     *
     * @return void
     */
    public function testSeeds()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', [ '--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', [ '--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', [ '--class' => \Database\Seeders\ConversationTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', [ '--class' => \Database\Seeders\MessageTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', [ '--class' => \Database\Seeders\AssignSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', [ '--class' => \Database\Seeders\InstitutionsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', [ '--class' => \Database\Seeders\UsersTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', [ '--class' => \Database\Seeders\VisitorsTableSeeder::class])->assertExitCode(0);
    }
}
