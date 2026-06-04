<?php

namespace App\Models\Moderation;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Auth\User;

class ModerationLog extends Model
{
    use HasUuids;

    protected $table = 'moderation_logs';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['moderator_id', 'target_user_id', 'action_type', 'reason', 'notes', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}