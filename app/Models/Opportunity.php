<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Opportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','opportunity_type_id','organization_id','prospect','eligibility_criteria',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    public function type()         { return $this->belongsTo(OpportunityType::class, 'opportunity_type_id'); }
    public function organization() { return $this->belongsTo(Organization::class); }
    public function contacts()     { return $this->hasMany(OpportunityContact::class); }
}
