<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(MessageTableSeeder::class);
        $user = User::latest('id')->first();

        $conversation = Conversation::latest('id')->first();
        $conversation->user()->associate($user);
        $conversation->save();
    }
}
