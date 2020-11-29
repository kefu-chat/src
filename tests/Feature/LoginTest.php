<?php

namespace Tests\Feature;

use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function testLoginOk()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', [ '--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', [ '--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);

        $login = $this->post(route('login', [], false), ['email' => AdminSeeder::ADMIN_EMAIL, 'password' => '123456']);
        $login->assertStatus(200);
        $login->assertJsonPath('success', true);
        $this->assertNotEmpty($login->json('data'));
        $this->assertNotEmpty($login->json('data.user'));
        $this->assertNotEmpty($login->json('data.institution'));
        $this->assertArrayHasKey('token', $login->json('data'));
        $login->assertJsonPath('data.token_type', 'Bearer');
    }

    public function testLoginWrong()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', [ '--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', [ '--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);

        $login = $this->post(route('login', [], false), ['email' => AdminSeeder::ADMIN_EMAIL, 'password' => '654321']);
        $login->assertStatus(422);
    }
}
