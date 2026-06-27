<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleChange extends Model
{
    protected $fillable = [
        'schedule_id',
        'reason_id',
        'user_id',
        'change_type',
        'previous_value',
        'new_value',
        'old_shift',
        'new_shift',
        'old_vehicle',
        'new_vehicle',
        'old_driver',
        'new_driver',
        'old_helpers',
        'new_helpers',
        'description',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function reason()
    {
        return $this->belongsTo(Reason::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}