<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ExecutiveAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id','position_id','scope_type','scope_id',
        'start_date','end_date','term_number',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function person()   { return $this->belongsTo(Person::class); }
    public function position() { return $this->belongsTo(ExecutivePosition::class, 'position_id'); }

    // Scope is polymorphic to Region/District/Constituency/Zone
    public function scope(): MorphTo { return $this->morphTo(); }
}
