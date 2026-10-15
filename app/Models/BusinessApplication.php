<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_id',
        'business_name',
        'business_type',
        'phone',
        'address',
        'outlet_name',
        'outlet_address',
        'status',
        'reviewed_by',
        'business_id',
        'review_note',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
