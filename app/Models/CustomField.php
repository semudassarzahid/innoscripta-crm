<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'position', 'required', 'type', 'company_id', 'custom_field_type_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function fieldType()
    {
        return $this->belongsTo(CustomFieldType::class, 'custom_field_type_id', 'id');
    }
}
