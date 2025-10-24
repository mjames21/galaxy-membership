<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MemberRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id','registration_number','registration_year',
        'region_id','district_id','constituency_id','zone_id',
    ];

    public function person()        { return $this->belongsTo(Person::class); }
    public function region()        { return $this->belongsTo(Region::class); }
    public function district()      { return $this->belongsTo(District::class); }
    public function constituency()  { return $this->belongsTo(Constituency::class); }
    public function zone()          { return $this->belongsTo(Zone::class); }
}
