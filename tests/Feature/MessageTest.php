<?php

namespace Tests\Feature;

use App\Broadcasting\ConversationAssigning;
use App\Broadcasting\ConversationIncoming;
use App\Broadcasting\ConversationMessaging;
use App\Models\Institution;
use App\Models\Message;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Str;
use Tests\TestCase;

class MessageTest extends TestCase
{
    /**
     * 测试表种子数据
     *
     * @return \Illuminate\Testing\TestResponse
     */
    public function testConversationsFromSeeders()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\VisitorsTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\ConversationTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\MessageTableSeeder::class])->assertExitCode(0);

        $listConversationRes = $this->get(route('conversation.list', ['type' => 'unassigned',], false), $this->authManager());
        $listConversationRes->assertStatus(200);
        $listConversationRes->assertJsonCount(1, 'data.conversations');

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
     * @return void
     */
    public function testMessagesFromSeeders()
    {
        $conversationRes = $this->testConversationsFromSeeders();
        $this->assertNotEmpty($conversationRes->json('data.messages'));
    }

    /**
     * 测试客服发消息
     */
    public function testMessageSendByAgent()
    {
        $conversationRes = $this->testConversationsFromSeeders();
        $conversation_id = $conversationRes->json('data.conversation.id');

        $message = Str::random();
        $sendConversationRes = $this->post(route('conversation.message.send', [$conversation_id], false), [
            'type' => Message::TYPE_TEXT,
            'content' => $message,
        ], $this->authManager());
        $sendConversationRes->assertStatus(200);
        $this->assertTrue($sendConversationRes->json('success'));
        $this->assertNotEmpty($sendConversationRes->json('data.message'));

        $getConversationRes = $this->get(route('conversation.message.list', [$conversation_id,], false), $this->authManager());
        $getConversationRes->assertStatus(200);
        $this->assertNotEmpty($getConversationRes->json('data.messages'));
        $this->assertContains($message, collect($getConversationRes->json('data.messages'))->pluck('content'));
    }

    /**
     * 测试访客发消息
     */
    public function testMessageSendByVisitor()
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

        $conversation_id = $initRes->json('data.conversation.id');

        $content = $generator->paragraph;

        Broadcast::shouldReceive('event')
            ->withArgs(fn ($arg) => $arg instanceof ConversationIncoming || $arg instanceof ConversationMessaging);

        $this->post(route('conversation.message.send', [$conversation_id], false), [
            'type' => Message::TYPE_TEXT,
            'content' => $content,
        ], $this->authVisitor())
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertSee($content);

        $this->get(route('conversation.message.list', [$conversation_id,], false), $this->authVisitor())
            ->assertOk()
            ->assertSee($content);
    }

    /**
     * 测试访客发的信息，客服能否看到
     */
    public function testAgentSeeVisitorMessage()
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

        $conversation_id = $initRes->json('data.conversation.id');

        $content = $generator->paragraph;

        Broadcast::shouldReceive('event')
            ->withArgs(fn ($arg) => $arg instanceof ConversationIncoming || $arg instanceof ConversationMessaging);

        $this->post(route('conversation.message.send', [$conversation_id,], false), [
            'type' => Message::TYPE_TEXT,
            'content' => $content,
        ], $this->authVisitor())
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertSee($content);

        $this->get(route('conversation.message.list', [$conversation_id,], false), $this->authManager())
            ->assertOk()
            ->assertSee($content);
    }

    /**
     * 测试客服发的信息，访客能否看到
     */
    public function testVisitorSeeAgentMessage()
    {
        $conversationRes = $this->testConversationsFromSeeders();
        $conversation_id = $conversationRes->json('data.conversation.id');


        $generator = app(Generator::class);
        $content = $generator->paragraph;

        Broadcast::shouldReceive('event')
            ->withArgs(fn ($arg) => $arg instanceof ConversationAssigning || $arg instanceof ConversationIncoming || $arg instanceof ConversationMessaging);

        // Broadcast::shouldReceive('event')
        //     ->once()
        //     ->withArgs(fn ($arg) => $arg instanceof ConversationMessaging && $arg->getMessage()->content === $content);

        $this->post(route('conversation.message.send', [$conversation_id], false), [
            'type' => Message::TYPE_TEXT,
            'content' => $content,
        ], $this->authManager())
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $getConversationRes = $this->get(route('conversation.message.list', [$conversation_id,], false), $this->authVisitor());
        $getConversationRes->assertStatus(200)
            ->assertSee($content);
    }
}
