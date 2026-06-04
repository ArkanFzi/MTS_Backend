<?php

namespace App\Models\Interaction;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Auth\User;

class Vote extends Model
{
    use HasUuids;

    protected $table = 'votes';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['user_id', 'target_id', 'target_type', 'vote_type', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function target()
    {
        return $this->morphTo(null, 'target_type', 'target_id');
    }
}