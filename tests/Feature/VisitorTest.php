<?php

namespace Tests\Feature;

use App\Models\Institution;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * 套餐和升级相关
 */
class VisitorTest extends TestCase
{
    /**
     * 当前套餐
     */
    public function testVisitorInit()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\InstitutionsTableSeeder::class])->assertExitCode(0);

        $institution = Institution::first();
        $generator = app(Generator::class);
        $url = $generator->url;

        $this->post(route('visitor.init', [
            'institution_id' => $institution->public_id,
            'unique_id' => Str::random(),
            'url' => $url,
            'languages' => ['zh-CN', 'en'],
            'userAgent' => 'PHPUnit',
        ], false))
            ->assertOk()
            ->assertSee('visitor_token')
            ->assertJsonPath('data.visitor_type', 'Berear')
            ->assertJsonPath('data.conversation.url', $url);
    }
}
