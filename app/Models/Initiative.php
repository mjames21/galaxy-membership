<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Initiative extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','brief_description','initiative_category_id','status_id','lead_id',
    ];

    public function category() { return $this->belongsTo(InitiativeCategory::class, 'initiative_category_id'); }
    public function status()   { return $this->belongsTo(InitiativeStatus::class); }
    public function lead()     { return $this->belongsTo(Person::class, 'lead_id'); }

    // Many-to-many: zones (pivot table: initiative_zone)
    public function zones()
    {
        return $this->belongsToMany(Zone::class, 'initiative_zone')->withTimestamps();
    }

    // Sponsors (pivot table: initiative_sponsors with sponsor_status targeted/secured)
    public function sponsors()
    {
        return $this->belongsToMany(Organization::class, 'initiative_sponsors')
                    ->withPivot('sponsor_status')
                    ->withTimestamps();
    }
    public function organizations()
{
    return $this->belongsToMany(Organization::class, 'initiative_organization')
                ->withPivot(['role'])
                ->withTimestamps();
}
}
