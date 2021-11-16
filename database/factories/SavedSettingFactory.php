<?php

namespace Settings\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Settings\DatabaseSettings\SavedSetting;

class SavedSettingFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SavedSetting::class;

    public function definition()
    {
        return [
            'key' => Str::random(40),
            'value' => Str::random(40),
            'model_id' => $this->faker->unique()->numberBetween(1, 100)
        ];
    }
}
