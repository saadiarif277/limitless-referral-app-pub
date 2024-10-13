<?php

namespace Database\Factories;

use App\Models\OrganizationType;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();
        $slug = str()->slug($name);
        return [
            'name' => $name,
            'slug' => $slug,
            'email' => fake()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->secondaryAddress(),
            'city' => fake()->city(),
            'state_id' => State::inRandomOrder()->first()->getKey(),
            'zip_code' => fake()->postcode(),
            'organization_type_id' => OrganizationType::inRandomOrder()->first()->getKey(),
        ];
    }
}
