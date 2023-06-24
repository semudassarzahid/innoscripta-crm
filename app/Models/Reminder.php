<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = ['lead_id', 'user_id', 'reminder_time', 'type'];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
