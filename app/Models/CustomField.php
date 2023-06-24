<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'type', 'company_id'
    ];

    public function company()
{
    return $this->belongsTo(Company::class);
}
}
