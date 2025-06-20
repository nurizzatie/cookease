<?php

namespace App\Services;

use App\Models\Bmi;

class CalorieCalculator
{
    public static function updateCalorieTarget(Bmi $bmi, ?string $goal = 'maintain_weight'): void
    {
        if ($bmi->gender === 'male') {
            $bmr = 10 * $bmi->weight + 6.25 * $bmi->height - 5 * $bmi->age + 5;
        } else {
            $bmr = 10 * $bmi->weight + 6.25 * $bmi->height - 5 * $bmi->age - 161;
        }

        $tdee = $bmr * 1.5;

        switch ($goal) {
            case 'lose_weight':
                $calories = $tdee - 500;
                break;
            case 'gain_weight':
                $calories = $tdee + 500;
                break;
            default:
                $calories = $tdee;
        }

        $bmi->calorie_target = round($calories);
        $bmi->save();
    }
}
