<?php

namespace Tests\Feature;

use Database\Seeders\AdminSeeder;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\MiniProgram\Auth\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginMiniappTest extends TestCase
{
    public function testLoginOk()
    {
        $container = new ServiceContainer(['app_id' => 'mock-id', 'secret' => 'mock-secret']);
        $client = $this->mockApiClient(Client::class, [], $container);

        $client->expects()->httpGet('sns/jscode2session', [
            'appid' => 'mock-id',
            'secret' => 'mock-secret',
            'js_code' => 'js-code',
            'grant_type' => 'authorization_code',
        ])->andReturn([
            'openid' => AdminSeeder::ADMIN_OPENID
        ]);

        app()->forgetInstance('wechat.mini_program.auth');
        app()->singleton('wechat.mini_program.auth', fn () => $client);

        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);

        $login = $this->post(route('login.via.miniapp', [], false), ['code' => 'js-code']);
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
        $container = new ServiceContainer(['app_id' => 'mock-id', 'secret' => 'mock-secret']);
        $client = $this->mockApiClient(Client::class, [], $container);

        $client->expects()->httpGet('sns/jscode2session', [
            'appid' => 'mock-id',
            'secret' => 'mock-secret',
            'js_code' => 'bad-js-code',
            'grant_type' => 'authorization_code',
        ])->andReturn([
            'errmsg' => 'bad code.'
        ]);

        app()->forgetInstance('wechat.mini_program.auth');
        app()->singleton('wechat.mini_program.auth', fn () => $client);

        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);

        $login = $this->post(route('login.via.miniapp', [], false), ['code' => 'bad-js-code']);
        $login->assertStatus(422);
    }
}
