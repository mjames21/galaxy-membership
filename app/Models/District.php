<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['region_id','name','code'];

    public function region()         { return $this->belongsTo(Region::class); }
    public function constituencies() { return $this->hasMany(Constituency::class); }
}
