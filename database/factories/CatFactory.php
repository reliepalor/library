<?php

namespace Database\Factories;

use App\Models\DojoCat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cat>
 */
class CatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'breed' => $this->faker->randomElement([
                'Siamese', 'Persian', 'Maine Coon', 'Bengal', 'Sphynx', 
                'Ragdoll', 'British Shorthair', 'Scottish Fold', 'Abyssinian', 'Savannah'
            ]),
            'age' => $this->faker->numberBetween(0, 15),
            'dojocat_id' => DojoCat::inRandomOrder()->first()->id ?? DojoCat::factory()->create()->id,

        ];
    }
}
