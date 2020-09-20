<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * 测试档案上传
 */
class FileUploadTest extends TestCase
{
    /**
     * 客服上传
     */
    public function testDashboardUpload()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);

        Storage::fake('oss');

        $response = $this->json('POST', route('file.upload'), [
            'file' => UploadedFile::fake()->image('avatar.jpg')
        ], $this->authManager());

        $response->assertOk();
        $response->assertJsonPath('success', true);

        // Assert the file was stored...
        Storage::disk('oss')->assertExists($response->json('data.path'));
    }

    /**
     * 访客上传
     */
    public function testVisitorUpload()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\VisitorsTableSeeder::class])->assertExitCode(0);

        Storage::fake('oss');

        $response = $this->json('POST', route('file.upload'), [
            'file' => UploadedFile::fake()->image('avatar.jpg')
        ], $this->authVisitor());

        $response->assertOk();
        $response->assertJsonPath('success', true);

        // Assert the file was stored...
        Storage::disk('oss')->assertExists($response->json('data.path'));
    }
}
