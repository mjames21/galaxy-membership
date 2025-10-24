<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OpportunityType extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
}
