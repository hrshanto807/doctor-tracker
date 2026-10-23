<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'age',
        'gender',
        'phone',
        'doctor_id',
        'condition',
        'admission_date',
        'room',
        'notes',
    ];

    protected $casts = [
        'admission_date' => 'date',
    ];

    /**
     * Attending doctor assigned to this patient.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
