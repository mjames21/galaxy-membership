<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizationAffiliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id','affiliation_level_id','description','notes',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function level()        { return $this->belongsTo(OrganizationAffiliationLevel::class, 'affiliation_level_id'); }
}
