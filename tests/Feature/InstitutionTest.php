<?php

namespace Tests\Feature;

use App\Models\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * 测试站点资料用例
 */
class InstitutionTest extends TestCase
{
    /**
     * 创建站点用例
     */
    public function testCreate()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\InstitutionsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);

        $name = Str::random();
        $website = 'https://test.com';
        $createRes = $this->post(route('institution.create', [], false), ['name' => $name, 'website' => $website,], $this->authManager())
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.institution.name', $name)
            ->assertJsonPath('data.institution.website', $website);

        $this->get(route('institution.show', [$createRes->json('data.institution.id')], false), $this->authManager())
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.institution.name', $name)
            ->assertJsonPath('data.institution.website', $website);
    }

    /**
     * 修改站点资料用例
     */
    public function testUpdate()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\InstitutionsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);

        $name = Str::random();
        $website = 'https://test.com';
        $this->post(route('institution.update', [Institution::latest('id')->firstOrFail()], false), ['name' => $name, 'website' => $website,], $this->authManager())
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->get(route('institution.show', [Institution::latest('id')->firstOrFail()], false), $this->authManager())
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.institution.name', $name)
            ->assertJsonPath('data.institution.website', $website);
    }

    /**
     * 删除站点用例
     */
    public function testDelete()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\InstitutionsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);

        $name = Str::random();
        $website = 'https://test.com';
        $createRes = $this->post(route('institution.create', [], false), ['name' => $name, 'website' => $website,], $this->authManager())
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.institution.name', $name)
            ->assertJsonPath('data.institution.website', $website);

        $this->post(route('institution.delete', [$createRes->json('data.institution.id')], [], false), [], $this->authManager())
            ->assertOk()
            ->assertJsonPath('success', true);


        $this->get(route('institution.show', [$createRes->json('data.institution.id')], false), $this->authManager())
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }

    /**
     * 非管理客服不允许创建站点
     */
    public function testNonManagerCreate()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\InstitutionsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\UsersTableSeeder::class])->assertExitCode(0);

        $name = Str::random();
        $website = 'https://test.com';
        $this->post(route('institution.create', [], false), ['name' => $name, 'website' => $website,], $this->authSupport())
            ->assertForbidden();
    }

    /**
     * 非管理客服不允许删除站点
     */
    public function testNonManagerDelete()
    {

        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\InstitutionsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\UsersTableSeeder::class])->assertExitCode(0);

        $name = Str::random();
        $website = 'https://test.com';
        $createRes = $this->post(route('institution.create', [], false), ['name' => $name, 'website' => $website,], $this->authManager())
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.institution.name', $name)
            ->assertJsonPath('data.institution.website', $website);

        $this->post(route('institution.delete', [$createRes->json('data.institution.id')], [], false), [], $this->authSupport())
            ->assertForbidden();
    }
}
