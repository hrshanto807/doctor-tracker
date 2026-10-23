<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialization',
        'hospital',
        'phone',
        'email',
        'avatar',
        'joined_date',
        'status',
    ];

    protected $casts = [
        'joined_date' => 'date',
    ];

    /**
     * Corresponding patients assigned to this doctor.
     */
    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }
}
