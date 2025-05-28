<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CalorieCalculator; 
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

    // Save or update the health goal
    HealthGoal::updateOrCreate(
        ['user_id' => $user->id],
        ['goal' => $request->goal]
    );

    // Get latest BMI for the user
    $bmi = Bmi::where('user_id', $user->id)->latest()->first();

    // Use the service to update calorie target if BMI exists
    if ($bmi) {
        CalorieCalculator::updateCalorieTarget($bmi, $request->goal);
    }

    return redirect()->back()->with('status', 'health-goal-updated');
}

}
