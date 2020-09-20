<?php

namespace Tests\Feature;

use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
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
    public function testPlanUpgradeWithBadCoupon()
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

        $this->post(route('enterprise.plan.upgrade', [$plan['id']], false), ['period' => 'monthly', 'coupon' => 'aaa',], $this->authManager())
            ->assertNotFound();
    }

    /**
     * 升级套餐下单+错误代金券
     */
    public function testPlanUpgradeWithCoupon()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PlansTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\EnterprisesTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\InstitutionsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\CounponsTableSeeder::class])->assertExitCode(0);

        $res = $this->get(route('enterprise.plan.show', false), $this->authManager())
            ->assertOk();

        $this->assertNotEmpty($res->json('data.plans_available'));
        $plan = Arr::last($res->json('data.plans_available'));
        $this->assertNotEmpty($plan);
        $this->assertArrayHasKey('id', $plan);

        $coupon = Coupon::where(['name' => 'onetime',])->first();
        $post = $this->post(route('enterprise.plan.upgrade', [$plan['id']], false), ['period' => 'monthly', 'coupon' => $coupon->public_id,], $this->authManager())
            ->assertOk()
            ->assertSee('order')
            ->assertJsonPath('data.order.need_pay_price', $coupon->type == Coupon::TYPE_DECR ? bcsub($plan['price_monthly'], $coupon->amount, 2): bcmul($plan['price_monthly'], $coupon->amount, 2));

        $this->assertArrayHasKey('price', $post->json('data.order'));
        $this->assertArrayHasKey('status', $post->json('data.order'));
    }

    /**
     * 升级套餐下单+错误代金券
     */
    public function testPlanUpgradeWithOnceCoupon()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PlansTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\EnterprisesTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\InstitutionsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\CounponsTableSeeder::class])->assertExitCode(0);

        $res = $this->get(route('enterprise.plan.show', false), $this->authManager())
            ->assertOk();

        $this->assertNotEmpty($res->json('data.plans_available'));
        $plan = Arr::last($res->json('data.plans_available'));
        $this->assertNotEmpty($plan);
        $this->assertArrayHasKey('id', $plan);

        $coupon = Coupon::where(['name' => 'onetime',])->first();
        $post = $this->post(route('enterprise.plan.upgrade', [$plan['id']], false), ['period' => 'monthly', 'coupon' => $coupon->public_id,], $this->authManager())
            ->assertOk()
            ->assertSee('order');

        $this->assertArrayHasKey('price', $post->json('data.order'));
        $this->assertArrayHasKey('status', $post->json('data.order'));

        $this->post(route('enterprise.plan.upgrade', [$plan['id']], false), ['period' => 'monthly', 'coupon' => $coupon->public_id,], $this->authManager())
            ->assertStatus(422);
    }

    /**
     * 升级套餐下单+给其他企业发的代金券
     */
    public function testPlanUpgradeWithEnterpriseCoupon()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PlansTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\EnterprisesTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\InstitutionsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\EnterprisesTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\CounponsTableSeeder::class])->assertExitCode(0);

        $res = $this->get(route('enterprise.plan.show', false), $this->authManager())
            ->assertOk();

        $this->assertNotEmpty($res->json('data.plans_available'));
        $plan = Arr::last($res->json('data.plans_available'));
        $this->assertNotEmpty($plan);
        $this->assertArrayHasKey('id', $plan);

        $coupon = Coupon::where(['name' => 'enterprise',])->first();
        $this->post(route('enterprise.plan.upgrade', [$plan['id']], false), ['period' => 'monthly', 'coupon' => $coupon->public_id,], $this->authManager())
            ->assertStatus(422);
    }

    /**
     * 升级套餐下单+给其他套餐发的代金券
     */
    public function testPlanUpgradeWithPlanCoupon()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PlansTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\EnterprisesTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\InstitutionsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\CounponsTableSeeder::class])->assertExitCode(0);

        $res = $this->get(route('enterprise.plan.show', false), $this->authManager())
            ->assertOk();

        $this->assertNotEmpty($res->json('data.plans_available'));
        $coupon = Coupon::where(['name' => 'plan',])->first();
        $plan = collect($res->json('data.plans_available'))->where('id', '<>', $coupon->plan->public_id)->last();
        $this->assertNotEmpty($plan);
        $this->assertArrayHasKey('id', $plan);

        $this->post(route('enterprise.plan.upgrade', [$plan['id']], false), ['period' => 'monthly', 'coupon' => $coupon->public_id,], $this->authManager())
            ->assertStatus(422);
    }
}
