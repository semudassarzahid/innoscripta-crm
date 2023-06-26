<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['subject', 'email_body', 'push_body', 'slug'];

    public function company()
{
    return $this->belongsTo(Company::class);
}
}
