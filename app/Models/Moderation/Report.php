<?php

namespace App\Models\Moderation;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Auth\User;

class Report extends Model
{
    use HasUuids;

    protected $table = 'reports';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'reporter_id',
        'target_id',
        'target_type',
        'reason',
        'description',
        'status',
        'resolved_by',
        'created_at',
        'resolved_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'resolved_at' => 'datetime'
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function target()
    {
        return $this->morphTo(null, 'target_type', 'target_id');
    }
}