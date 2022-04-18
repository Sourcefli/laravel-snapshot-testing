<?php

namespace Sourcefli\SnapshotTesting\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Post extends Model
{
	use HasFactory;

	protected static function newFactory(): Factory
	{
		return new class extends Factory {
			public function definition(): array
			{
				return [
					'title' => $this->faker->sentence(),
					'body' => $this->faker->text(rand(20, 150)),
					'user_id' => User::factory(),
					'published_at' => Arr::random([null, null, $this->faker->dateTimeBetween('-1 year', '-1 week')])
				];
			}
		};
	}
}
