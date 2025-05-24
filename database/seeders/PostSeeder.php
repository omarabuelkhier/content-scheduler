<?php

namespace Database\Seeders;

use App\Models\Platform;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = Platform::all();
        $users = User::all();

        foreach ($users as $user) {
            $posts = Post::factory(5)->create(['user_id' => $user->id]);

            foreach ($posts as $post) {
                $post->platforms()->attach(
                    $platforms->random(rand(1, 3))->pluck('id')->toArray(),
                    ['platform_status' => 'pending']
                );
            }
        }
    }
}
