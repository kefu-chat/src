<?php

namespace Tests\Feature;

use App\Broadcasting\ConversationAssigning;
use App\Broadcasting\ConversationIncoming;
use App\Broadcasting\ConversationMessaging;
use App\Broadcasting\ConversationTerminated;
use App\Models\Institution;
use App\Models\Message;
use App\Models\User;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ConversationTest extends TestCase
{
    /**
     * 测试多个会话
     *
     * @return \Illuminate\Testing\TestResponse
     */
    public function testMultiConversationsFromSeeders()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\VisitorsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\ConversationTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\MessageTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\VisitorsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\ConversationTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\MessageTableSeeder::class])->assertExitCode(0);

        $listConversationRes = $this->get(route('conversation.list', ['type' => 'unassigned',], false), $this->authManager());
        $listConversationRes->assertStatus(200);
        $listConversationRes->assertJsonCount(2, 'data.conversations');

        $conversation_id = $listConversationRes->json('data.conversations.0.id');

        $getConversationRes = $this->get(route('conversation.message.list', [$conversation_id, 'type' => 'unassigned',], false), $this->authManager());
        $getConversationRes->assertStatus(200);
        $this->assertNotEmpty($getConversationRes->json('data.conversation'));
        $this->assertNotEmpty($getConversationRes->json('data.messages'));
        $this->assertFalse($getConversationRes->json('data.has_previous'));

        return $getConversationRes;
    }

    /**
     * 测试表种子数据
     *
     * @return \Illuminate\Testing\TestResponse
     */
    public function testConversationTransfer()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\UsersTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\VisitorsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\ConversationTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\MessageTableSeeder::class])->assertExitCode(0);

        $listConversationRes = $this->get(route('conversation.list', ['type' => 'unassigned',], false), $this->authManager());
        $listConversationRes->assertStatus(200);
        $listConversationRes->assertJsonCount(1, 'data.conversations');

        $conversation_id = $listConversationRes->json('data.conversations.0.id');

        /**
         * @var Generator $generator
         */
        $generator = app(Generator::class);
        $content = $generator->paragraph;

        dd($this->post(route('conversation.message.send', [$conversation_id], false), [
            'type' => Message::TYPE_TEXT,
            'content' => $content,
        ], $this->authSupport())->json());
        $this->post(route('conversation.message.send', [$conversation_id], false), [
            'type' => Message::TYPE_TEXT,
            'content' => $content,
        ], $this->authSupport())
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertSee($content);

        $manager = User::permission(Permission::findByName('manager', 'api'))->first();

        $this->get(route('conversation.transfer', [$conversation_id, $manager,], false), $this->authSupport())
            ->assertOk();

        $this->get(route('conversation.message.list', [$conversation_id,], false), $this->authManager())
            ->assertOk()
            ->assertJsonPath('data.conversation.user.id', $manager->public_id);
    }

    /**
     * 拉取待打招呼测试
     */
    public function testListUngreeted()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\InstitutionsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);


        $institution = Institution::first();
        $generator = app(Generator::class);
        $url = $generator->url;
        $unique_id = Str::random();
        $content = $generator->paragraph;

        $initRes = $this->post(route('visitor.init', [
            'institution_id' => $institution->public_id,
            'unique_id' => $unique_id,
            'url' => $url,
            'languages' => ['zh-CN', 'en'],
            'userAgent' => 'PHPUnit',
        ], false))
            ->assertOk()
            ->assertSee('visitor_token')
            ->assertJsonPath('data.visitor_type', 'Berear')
            ->assertJsonPath('data.conversation.url', $url);


        $this->get(route('conversation.list', ['type' => 'unassigned',], false), $this->authManager())
            ->assertOk()
            ->assertDontSee($unique_id);

        $this->get(route('conversation.list-ungreeted', ['type' => 'online',], false), $this->authManager())
            ->assertOk()
            ->assertSee($unique_id);

        $this->post(route('conversation.message.send', [$initRes->json('data.conversation.id')], false), [
            'type' => Message::TYPE_TEXT,
            'content' => $content,
        ], $this->authVisitor())
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertSee($content);

        $this->get(route('conversation.list-ungreeted', ['type' => 'online',], false), $this->authManager())
            ->assertOk()
            ->assertDontSee($unique_id);
    }

    public function testTerminate()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);

        $institution = Institution::first();
        $generator = app(Generator::class);
        $url = $generator->url;
        $unique_id = Str::random();

        $initRes = $this->post(route('visitor.init', [
            'institution_id' => $institution->public_id,
            'unique_id' => $unique_id,
            'url' => $url,
            'languages' => ['zh-CN', 'en'],
            'userAgent' => 'PHPUnit',
        ], false))
            ->assertOk();

        $content = $generator->paragraph;

        Broadcast::shouldReceive('event')
            ->withArgs(fn ($arg) => $arg instanceof ConversationAssigning || $arg instanceof ConversationIncoming || $arg instanceof ConversationMessaging);

        $this->post(route('conversation.message.send', [$initRes->json('data.conversation.id')], false), [
            'type' => Message::TYPE_TEXT,
            'content' => $content,
        ], $this->authVisitor())
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertSee($content);



        $conversation_id = $this->get(route('conversation.list', ['type' => 'unassigned',], false), $this->authManager())
            ->assertOk()
            ->assertSee($unique_id)
            ->json('data.conversations.0.id');

        Broadcast::shouldReceive('event')
            ->once()
            ->withArgs(fn ($arg) => $arg instanceof ConversationTerminated);

        $this->post(route('conversation.terminate', $conversation_id), [])->assertOk();


        $this->post(route('conversation.message.send', [$initRes->json('data.conversation.id')], false), [
            'type' => Message::TYPE_TEXT,
            'content' => $content,
        ], $this->authVisitor())
          ->assertStatus(400);
    }
}
