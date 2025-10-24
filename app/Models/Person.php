<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Person extends Model
{
    use HasFactory;

    // allow create/update with these fields
    protected $fillable = [
        'first_name',
        'last_name',
        'other_names',
        'email',
        'phone',
        'address',
    ];

    // optional niceties
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // relations you use elsewhere
    public function memberRegistrations()
    {
        return $this->hasMany(MemberRegistration::class);
    }
}
