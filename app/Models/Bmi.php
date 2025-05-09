<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bmi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'age',
        'gender',
        'height',
        'weight',
    ];

    // Optional: Set up the relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Accessor to calculate BMI
    public function getBmiAttribute()
    {
        if ($this->height > 0) {
            return round($this->weight / (($this->height / 100) ** 2), 2);
        }

        return null;
    }
}