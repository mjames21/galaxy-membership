<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Constituency extends Model
{
    use HasFactory;

    protected $fillable = ['district_id','name','code'];

    public function district() { return $this->belongsTo(District::class); }
    public function zones()    { return $this->hasMany(Zone::class); }
}
