<?php

use App\Broadcasting\ConversationMessaging;
use App\Models\Conversation;
use App\Models\Institution;
use App\Models\Message;
use App\Models\User;
use App\Models\Visitor;
use Faker\Generator;
use Illuminate\Database\Seeder;

class MessageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $generator)
    {
        begin:
        $conversation = Conversation::latest()->first();
        if (!$conversation) {
            $this->call(ConversationTableSeeder::class);
            goto begin;
        }

        $visitor = Visitor::latest()->first();
        $user = User::latest()->first();
        $institution = Institution::latest()->first();

        $count = mt_rand(1, 3);
        for ($i = 0; $i < $count; $i ++) {
            $type = mt_rand(1, 2);
            $message = new Message([
                'content' => $type == 1 ? $generator->paragraph(mt_rand(1, 7)) : $generator->imageUrl(),
                'type' => $type,
            ]);
            $message->institution()->associate($institution);
            $message->conversation()->associate($conversation);
            $message->sender()->associate($user);
            $message->save();

            broadcast(new ConversationMessaging($message));
        }

        $count = mt_rand(1, 3);
        for ($i = 0; $i < $count; $i++) {
            $type = mt_rand(1, 2);
            $message = new Message([
                'content' => $type == 1 ? $generator->paragraph(mt_rand(1, 7)) : $generator->imageUrl(),
                'type' => $type,
            ]);
            $message->institution()->associate($institution);
            $message->conversation()->associate($conversation);
            $message->sender()->associate($visitor);
            $message->save();

            broadcast(new ConversationMessaging($message));
        }

        echo $conversation->id . PHP_EOL;
    }
}
