<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stakeholder extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id','stakeholder_category_id','organization_id',
        'party_affiliation_id','willingness_level_id',
        'district_id','constituency_id','zone_id',
    ];

    public function person()            { return $this->belongsTo(Person::class); }
    public function category()          { return $this->belongsTo(StakeholderCategory::class, 'stakeholder_category_id'); }
    public function organization()      { return $this->belongsTo(Organization::class); }
    public function partyAffiliation()  { return $this->belongsTo(PartyAffiliation::class); }
    public function willingness()       { return $this->belongsTo(WillingnessLevel::class, 'willingness_level_id'); }
    public function district()          { return $this->belongsTo(District::class); }
    public function constituency()      { return $this->belongsTo(Constituency::class); }
    public function zone()              { return $this->belongsTo(Zone::class); }

    // Many-to-many (pivot: stakeholder_support_type)
    public function supportTypes()
    {
        return $this->belongsToMany(SupportType::class, 'stakeholder_support_type')
                    ->withTimestamps();
    }
}
