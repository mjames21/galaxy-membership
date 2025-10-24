<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = ['constituency_id','name','code'];

    public function constituency() { return $this->belongsTo(Constituency::class); }
}
