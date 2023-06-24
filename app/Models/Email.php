<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = ['subject', 'body', 'slug'];

    public function company()
{
    return $this->belongsTo(Company::class);
}
}
