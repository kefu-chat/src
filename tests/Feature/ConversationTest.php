<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ConversationTest extends TestCase
{
    /**
     * 测试表种子数据
     *
     * @return void
     */
    public function testConversationsFromSeeders()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', [ '--class' => \Database\Seeders\PermissionSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', [ '--class' => \Database\Seeders\AdminSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', [ '--class' => \Database\Seeders\ConversationTableSeeder::class])->assertExitCode(0);
        $this->artisan('db:seed', [ '--class' => \Database\Seeders\MessageTableSeeder::class])->assertExitCode(0);

        $login_header = ['Authorization' => 'Bearer ' . JWTAuth::fromUser(User::firstOrFail())];

        $listConversationRes = $this->get(route('conversation.list.agent', ['type' => 'unassigned',], false), $login_header);
        $listConversationRes->assertStatus(200);
        $listConversationRes->assertJsonCount(1, 'data.conversations');

        $conversation_id = $listConversationRes->json('data.conversations.0.id');

        $getConversationRes = $this->get(route('conversation.message.list.agent', [$conversation_id,], false), $login_header);
        $getConversationRes->assertStatus(200);
        $this->assertTrue(!!$getConversationRes->json('data.conversation'));
        $this->assertTrue(!!$getConversationRes->json('data.messages'));
        $this->assertFalse($getConversationRes->json('data.has_previous'));
        ob_clean();
    }
}
