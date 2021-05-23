<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->realText(20),
            'content' => $this->faker->realText(100),
            'category_id' => rand(1, 10),
            'pic' => $this->faker->imageUrl,
            'sort' => rand(0, 10),
            'enabled' => rand(0, 1)
        ];
    }
}
