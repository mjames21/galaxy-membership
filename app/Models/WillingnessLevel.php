<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WillingnessLevel extends Model
{
    use HasFactory;
    protected $fillable = ['name','rank'];
}
