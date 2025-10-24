<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = ['name','type','email','phone','address','description'];

    public function contacts()           { return $this->hasMany(OrganizationContact::class); }
    public function affiliations()       { return $this->hasMany(OrganizationAffiliation::class); }
    public function latestAffiliation()  { return $this->hasOne(OrganizationAffiliation::class)->latestOfMany(); }
    public function initiatives()
{
    return $this->belongsToMany(Initiative::class, 'initiative_organization')
                ->withPivot(['role'])
                ->withTimestamps();
}
}
