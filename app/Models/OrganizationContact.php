<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizationContact extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id','person_id','designation'];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function person()       { return $this->belongsTo(Person::class); }
}
