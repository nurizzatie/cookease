<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MealPlan;
use App\Notifications\DailyMealPlanReminder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SomeController extends Controller
{
    // Method to send notifications for today's meal plans
    public function sendTodayMealPlanNotifications()
    {
        $today = Carbon::today()->toDateString();

        $users = User::all();

        foreach ($users as $user) {
            $mealPlansForToday = MealPlan::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->get();

            foreach ($mealPlansForToday as $mealPlan) {
                $user->notify(new DailyMealPlanReminder($mealPlan->meal_type, $user->name));
            }
        }

        return "Notifications sent!";
    }
}

// Your Notification class can remain in its usual folder: App\Notifications
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DailyMealPlanReminder extends Notification
{
    use Queueable;

    protected $mealType;
    protected $userName;

    public function __construct($mealType, $userName)
    {
        $this->mealType = $mealType;
        $this->userName = $userName;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $mealTypeNames = [
            'breakfast' => 'Breakfast',
            'lunch' => 'Lunch',
            'dinner' => 'Dinner',
            'snack' => 'Snack',
            'snacks' => 'Snack',
            'others' => 'Other',
            'other' => 'Other',
        ];

        $normalizedMealType = strtolower(trim($this->mealType));

        \Log::info('DailyMealPlanReminder mealType:', ['mealType' => $this->mealType]);

        $mealName = $mealTypeNames[$normalizedMealType] ?? ucfirst($normalizedMealType);

        return [
            'title' => 'Meal Plan Reminder',
            'message' => "Hello {$this->userName}, you have a meal plan {$mealName} reminder today.",
        ];
    }
}
