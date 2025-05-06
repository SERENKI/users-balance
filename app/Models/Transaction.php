<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'description',
        'status',
        'processed_at'
    ];
    protected $casts = [
        'type'      => TransactionType::class,
        'status'    => TransactionStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', TransactionStatus::COMPLETED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', TransactionStatus::FAILED);
    }
}
