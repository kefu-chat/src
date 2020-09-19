<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationTest extends TestCase
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

        $listConversationRes = $this->get(route('conversation.list.agent', ['type' => 'unassigned',], false), $this->authManager());
        $listConversationRes->assertStatus(200);
        $listConversationRes->assertJsonCount(1, 'data.conversations');

        $conversation_id = $listConversationRes->json('data.conversations.0.id');

        $getConversationRes = $this->get(route('conversation.message.list.agent', [$conversation_id, 'type' => 'unassigned',], false), $this->authManager());
        $getConversationRes->assertStatus(200);
        $this->assertNotEmpty($getConversationRes->json('data.conversation'));
        $this->assertNotEmpty($getConversationRes->json('data.messages'));
        $this->assertFalse($getConversationRes->json('data.has_previous'));

        return $getConversationRes;
    }

    /**
     * 测试多个会话
     *
     * @return \Illuminate\Testing\TestResponse
     */
    public function testMuitiConversationsFromSeeders()
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

        $listConversationRes = $this->get(route('conversation.list.agent', ['type' => 'unassigned',], false), $this->authManager());
        $listConversationRes->assertStatus(200);
        $listConversationRes->assertJsonCount(2, 'data.conversations');

        $conversation_id = $listConversationRes->json('data.conversations.0.id');

        $getConversationRes = $this->get(route('conversation.message.list.agent', [$conversation_id, 'type' => 'unassigned',], false), $this->authManager());
        $getConversationRes->assertStatus(200);
        $this->assertNotEmpty($getConversationRes->json('data.conversation'));
        $this->assertNotEmpty($getConversationRes->json('data.messages'));
        $this->assertFalse($getConversationRes->json('data.has_previous'));

        return $getConversationRes;
    }
}
