<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'language',
        'user_message',
        'ai_response',
        'intent',
        'is_emergency_escalated',
        'context',
    ];

    protected $casts = [
        'is_emergency_escalated' => 'boolean',
        'context' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
