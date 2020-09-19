<?php

namespace Tests\Feature;

use App\Models\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * 测试站点资料用例
 */
class InstitutionProfileTest extends TestCase
{
    /**
     * 修改站点资料用例
     */
    public function testUpdateProfile()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\InstitutionsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);

        $name = Str::random();
        $website = 'https://test.com';
        $this->post(route('institution.profile.update', [Institution::latest('id')->firstOrFail()], false), ['name' => $name, 'website' => $website,], $this->authManager())
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->get(route('institution.profile.show', [Institution::latest('id')->firstOrFail()], false), $this->authManager())
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.institution.name', $name)
            ->assertJsonPath('data.institution.website', $website);
    }
}
