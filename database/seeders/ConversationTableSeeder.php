<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Institution;
use App\Models\User;
use App\Models\Visitor;
use Faker\Generator;
use Illuminate\Database\Seeder;

class ConversationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $generator)
    {
        begin:
        $visitor = Visitor::latest()->first();
        if (!$visitor) {
            $this->call(VisitorsTableSeeder::class);
            goto begin;
        }
        $user = User::latest()->first();
        if (!$user) {
            $this->call(UsersTableSeeder::class);
            goto begin;
        }
        $institution = Institution::latest()->first();
        if (!$user) {
            $this->call(InstitutionsTableSeeder::class);
            goto begin;
        }

        $conversation = new Conversation([
            'ip' => $generator->ipv4,
            'url' => $generator->url,
            'first_reply_at' => $generator->dateTimeThisYear,
            'last_reply_at' => $generator->dateTimeThisYear,
        ]);
        $conversation->institution()->associate($institution);
        $conversation->visitor()->associate($visitor);
        //$conversation->user()->associate($user);
        $conversation->save();

        echo $conversation->id . PHP_EOL;
    }
}
