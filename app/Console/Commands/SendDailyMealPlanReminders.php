<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\MealPlan;
use App\Notifications\DailyMealPlanReminder;
use Carbon\Carbon;

class SendDailyMealPlanReminders extends Command
{
    protected $signature = 'mealplan:notify';
    protected $description = 'Send daily meal plan reminders to users';

    public function handle()
    {
        $today = Carbon::today()->toDateString();

        $users = User::all();

        foreach ($users as $user) {
            $plans = MealPlan::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->get();

            foreach ($plans as $plan) {
                $user->notify(new DailyMealPlanReminder($plan->meal_type, $today));
            }
        }

        $this->info('✅ Daily meal plan notifications sent successfully.');
    }
}