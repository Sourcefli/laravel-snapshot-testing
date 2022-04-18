<?php

namespace Sourcefli\SnapshotTesting\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class User extends Model
{
	use HasFactory;

	protected static function newFactory()
	{
		return new class extends Factory {
			public function definition()
			{
				return [
					'name' => $this->faker->name(),
					'email' => $this->faker->email(),
					'password' => Hash::make('password'),
					'email_verified_at' => Arr::random([null, null, $this->faker->dateTimeBetween('-1 year', '-1 week')])
				];
			}
		};
	}
}
