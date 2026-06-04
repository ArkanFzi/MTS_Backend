<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Auth\User;

class PointsLog extends Model
{
    use HasUuids;

    protected $table = 'points_log';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['user_id', 'points', 'action_type', 'reference_id', 'description', 'created_at'];

    protected $casts = [
        'points' => 'integer',
        'created_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}