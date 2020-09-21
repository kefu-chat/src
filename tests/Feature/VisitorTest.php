<?php

namespace Tests\Feature;

use App\Models\Institution;
use App\Models\Message;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * 访客用例
 */
class VisitorTest extends TestCase
{
    /**
     * 访客初始化
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

    public function testVisitorMessage()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);

        $institution = Institution::first();
        $generator = app(Generator::class);
        $url = $generator->url;

        $initRes = $this->post(route('visitor.init', [
            'institution_id' => $institution->public_id,
            'unique_id' => Str::random(),
            'url' => $url,
            'languages' => ['zh-CN', 'en'],
            'userAgent' => 'PHPUnit',
        ], false))
            ->assertOk();

        $this->post(route('conversation.message.send', [$initRes->json('data.conversation.id')], false), [
            'type' => Message::TYPE_TEXT,
            'content' => 'test',
        ], $this->authVisitor())
            ->assertOk()
            ->assertSee('test');
    }

    public function testSupportUpdateVisitorInfo()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\UsersTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);

        $institution = Institution::first();
        $generator = app(Generator::class);
        $url = $generator->url;

        $initRes = $this->post(route('visitor.init', [
            'institution_id' => $institution->public_id,
            'unique_id' => Str::random(),
            'url' => $url,
            'languages' => ['zh-CN', 'en'],
            'userAgent' => 'PHPUnit',
        ], false))
            ->assertOk();

        $name = $generator->name;
        $email = $generator->email;
        $phone = $generator->phoneNumber;
        $memo = $generator->paragraph;

        $this->post(route('visitor.update', [$initRes->json('data.conversation.visitor.id')], false), [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'memo' => $memo,
        ], $this->authSupport())
            ->assertOk()
            ->assertJsonPath('data.visitor.name', $name)
            ->assertJsonPath('data.visitor.email', $email)
            ->assertJsonPath('data.visitor.phone', $phone)
            ->assertJsonPath('data.visitor.memo', $memo);
    }

    public function testManagerUpdateVisitorInfo()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\UsersTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);

        $institution = Institution::first();
        $generator = app(Generator::class);
        $url = $generator->url;

        $initRes = $this->post(route('visitor.init', [
            'institution_id' => $institution->public_id,
            'unique_id' => Str::random(),
            'url' => $url,
            'languages' => ['zh-CN', 'en'],
            'userAgent' => 'PHPUnit',
        ], false))
            ->assertOk();

        $name = $generator->name;
        $email = $generator->email;
        $phone = $generator->phoneNumber;
        $memo = $generator->paragraph;

        $this->post(route('visitor.update', [$initRes->json('data.conversation.visitor.id')], false), [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'memo' => $memo,
        ], $this->authManager())
            ->assertOk()
            ->assertJsonPath('data.visitor.name', $name)
            ->assertJsonPath('data.visitor.email', $email)
            ->assertJsonPath('data.visitor.phone', $phone)
            ->assertJsonPath('data.visitor.memo', $memo);
    }
}
