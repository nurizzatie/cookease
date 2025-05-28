<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bmi;
use App\Models\HealthGoal;


class HealthGoalController extends Controller
{
    public function create()
    {
        return view('health_goals.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'goal' => 'required|in:lose_weight,gain_weight,maintain_weight',
        ]);

        $user = Auth::user();
        
        // Save or update the health goal in the health_goals table
        HealthGoal::updateOrCreate(
             ['user_id' => Auth::id()],
             ['goal' => $request->goal]
         );


        // Get latest BMI data for the user
        $bmi = Bmi::where('user_id', $user->id)->latest()->first();

        if ($bmi) {
            // Calculate BMR using Mifflin-St Jeor formula
            if ($bmi->gender === 'male') {
                $bmr = 10 * $bmi->weight + 6.25 * $bmi->height - 5 * $bmi->age + 5;
            } else {
                $bmr = 10 * $bmi->weight + 6.25 * $bmi->height - 5 * $bmi->age - 161;
            }

            // Estimate TDEE (assuming moderate activity)
            $tdee = $bmr * 1.5;

            // Adjust calorie based on goal
            switch ($request->goal) {
                case 'lose_weight':
                    $calories = $tdee - 500;
                    break;
                case 'gain_weight':
                    $calories = $tdee + 500;
                    break;
                default:
                    $calories = $tdee;
            }

            // Save calorie target to BMI record
            $bmi->calorie_target = round($calories);
            $bmi->save();
        }

        return redirect()->back()->with('status', 'health-goal-updated');
    }
}
