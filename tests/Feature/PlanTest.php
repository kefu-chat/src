<?php

namespace Tests\Feature;

use App\Models\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * 套餐和升级相关
 */
class PlanTest extends TestCase
{
    /**
     * 当前套餐
     */
    public function testPlanGet()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PlansTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\EnterprisesTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\InstitutionsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);

        $plan = $this->get(route('enterprise.plan.show', false), $this->authManager())
            ->assertOk()
            ->json('data.plan');

        $this->assertNotEmpty($plan);
    }

    /**
     * 升级套餐下单
     */
    public function testPlanUpgrade()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PlansTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\EnterprisesTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\InstitutionsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);

        $res = $this->get(route('enterprise.plan.show', false), $this->authManager())
            ->assertOk();

        $this->assertNotEmpty($res->json('data.plans_available'));
        $plan = Arr::last($res->json('data.plans_available'));
        $this->assertNotEmpty($plan);
        $this->assertArrayHasKey('id', $plan);

        $post = $this->post(route('enterprise.plan.upgrade', [$plan['id']], false), ['period' => 'monthly',], $this->authManager())
            ->assertOk()
            ->assertSee('order');

        $this->assertArrayHasKey('price', $post->json('data.order'));
        $this->assertArrayHasKey('status', $post->json('data.order'));
    }

    /**
     * 升级套餐下单+错误代金券
     */
    public function testPlanUpgradeBadCoupon()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PlansTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\EnterprisesTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\InstitutionsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);

        $res = $this->get(route('enterprise.plan.show', false), $this->authManager())
            ->assertOk();

        $this->assertNotEmpty($res->json('data.plans_available'));
        $plan = Arr::last($res->json('data.plans_available'));
        $this->assertNotEmpty($plan);
        $this->assertArrayHasKey('id', $plan);

        $post = $this->post(route('enterprise.plan.upgrade', [$plan['id']], false), ['period' => 'monthly', 'coupon' => 'aaa',], $this->authManager())
            ->assertNotFound();
    }
}
