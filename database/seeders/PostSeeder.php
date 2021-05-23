<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\PostTag;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::truncate();
        PostTag::truncate();
        $posts = Post::factory()->times(10)->create();
        foreach ($posts as $post) {
            $post->tags()->sync([rand(1, 10)]);
        }
    }
}
