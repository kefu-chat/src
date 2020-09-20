<?php

namespace Tests\Feature;

use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class MessageTest extends ConversationTest
{
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
        $conversationRes = $this->testConversationsFromSeeders();
        $conversation_id = $conversationRes->json('data.conversation.id');

        $message = Str::random();
        $sendConversationRes = $this->post(route('conversation.message.send', [$conversation_id], false), [
            'type' => Message::TYPE_TEXT,
            'content' => $message,
        ], $this->authVisitor());
        $sendConversationRes->assertStatus(200);
        $this->assertTrue($sendConversationRes->json('success'));
        $this->assertNotEmpty($sendConversationRes->json('data.message'));

        $getConversationRes = $this->get(route('conversation.message.list', [$conversation_id,], false), $this->authVisitor());
        $getConversationRes->assertStatus(200);
        $this->assertNotEmpty($getConversationRes->json('data.messages'));
        $this->assertContains($message, collect($getConversationRes->json('data.messages'))->pluck('content'));
    }

    /**
     * 测试访客发的信息，客服能否看到
     */
    public function testAgentSeeVisitorMessage()
    {
        $conversationRes = $this->testConversationsFromSeeders();
        $conversation_id = $conversationRes->json('data.conversation.id');

        $message = Str::random();
        $sendConversationRes = $this->post(route('conversation.message.send', [$conversation_id], false), [
            'type' => Message::TYPE_TEXT,
            'content' => $message,
        ], $this->authVisitor());
        $sendConversationRes->assertStatus(200);
        $this->assertTrue($sendConversationRes->json('success'));
        $this->assertNotEmpty($sendConversationRes->json('data.message'));

        $getConversationRes = $this->get(route('conversation.message.list', [$conversation_id,], false), $this->authManager());
        $getConversationRes->assertStatus(200);
        $this->assertNotEmpty($getConversationRes->json('data.messages'));
        $this->assertContains($message, collect($getConversationRes->json('data.messages'))->pluck('content'));
    }

    /**
     * 测试客服发的信息，访客能否看到
     */
    public function testVisitorSeeAgentMessage()
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

        $getConversationRes = $this->get(route('conversation.message.list', [$conversation_id,], false), $this->authVisitor());
        $getConversationRes->assertStatus(200);
        $this->assertNotEmpty($getConversationRes->json('data.messages'));
        $this->assertContains($message, collect($getConversationRes->json('data.messages'))->pluck('content'));
    }
}
