<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = ['name','position','is_default','company_id'];

    public function fromRules()
    {
        return $this->hasMany(WorkflowRule::class, 'from_status_id');
    }

    public function toRules()
    {
        return $this->hasMany(WorkflowRule::class, 'to_status_id');
    }
}
