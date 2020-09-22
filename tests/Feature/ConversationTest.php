<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
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
}
