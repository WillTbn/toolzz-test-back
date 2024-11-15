<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Arr;
class ChatTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $users = User::all();
        $one  = User::where('id', 1)->first()->id;
        $two  = User::where('id', 2)->first()->id;
        $three  = User::where('id', 3)->first()->id;
        $four  = User::where('id', 4)->first()->id;
        $five = User::where('id', 5)->first()->id;
        $six = User::where('id', 19)->first()->id;
        
        Chat::factory()->has(ChatMessage::factory(8)->state(function () use ($two, $one) {
            $ids = [$one, $two];
            $authorId = Arr::random($ids);
            $receiverId = $authorId === $ids[0] ? $ids[1] : $ids[0];
    
            return [
                'author_id' => (string)$authorId,
                'receiver_id' => (string)$receiverId,
            ];
        }))->create([
            'hash_id' =>  Hashids::encode(800),
            'user_one_id' => $one,
            'user_two_id' => $two,
        ]);
        Chat::factory()->has(ChatMessage::factory(11)->state(function () use ($four, $five){
            $ids = [$four, $five];
            $authorId = Arr::random($ids);
            $receiverId = $authorId === $ids[0] ? $ids[1] : $ids[0];
    
            return [
                'author_id' => (string)$authorId,
                'receiver_id' => (string)$receiverId,
            ];
        }))->create([
            'hash_id' =>  Hashids::encode(900),
            'user_one_id' =>$four,
            'user_two_id' =>  $five,
        ]);
        Chat::factory()->has(ChatMessage::factory(20)->state(function () use ($three, $six) {
            $ids = [$three,$six];
            $authorId = Arr::random($ids);
            $receiverId = $authorId === $ids[0] ? $ids[1] : $ids[0];
    
            return [
                'author_id' => $authorId,
                'receiver_id' => $receiverId,
            ];
        }))->create([
            'hash_id' =>  Hashids::encode(70002),
            'user_one_id' => $three,
            'user_two_id' => $six,
        ]);
        $this->command->info('Tudo certo maravilha!');
    }
}
