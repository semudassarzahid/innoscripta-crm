<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use App\Models\CustomField;
use App\Models\Status;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'company_id', 'status_id'
    ];

    public function customFields()
    {
        return $this->belongsToMany(CustomField::class, 'lead_custom_field_values')
            ->withPivot('value')
            ->withTimestamps();
    }

    public function getCustomFieldValue($customFieldId)
    {
        $customField = CustomField::find($customFieldId);

        if (!$customField || !Schema::hasColumn('lead_custom_field_values', 'value')) {
            return null;
        }

        $value = $this->customFields()
            ->where('custom_field_id', $customFieldId)
            ->value('value');

        switch ($customField->type) {
            case 'text':
            case 'textarea':
            case 'select':
                return $value;
            case 'checkbox':
                return (bool) $value;
            case 'number':
                return (float) $value;
            default:
                return null;
        }
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function company()
{
    return $this->belongsTo(Company::class);
}
}
