<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',
        'sender_id',
        'receiver_id',
        'receiver_delete_at',
        'sender_delete_at',
        'read_at',
        'conversation_id',
    ];

    protected $dates = ['read_at', 'receiver_delete_at', 'sender_delete_at', 'body'];

    // Functions
    public function isRead(): bool
    {
        return $this->read_at != null;
    }

    // Attributes
    public function getIsReadAttribute()
    {
        return !is_null($this->read_at);
    }

    public function getIsReceiverDeletedAttribute()
    {
        return !is_null($this->receiver_deleted_at);
    }

    public function getIsSenderDeletedAttribute()
    {
        return !is_null($this->sender_deleted_at);
    }

    // Relations
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
