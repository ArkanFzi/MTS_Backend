<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Auth\User;

class Badge extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'badges';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'icon_url',
        'tier',
        'condition_type',
        'condition_value',
        'created_at'
    ];

    protected $casts = [
        'condition_value' => 'integer',
        'created_at' => 'datetime'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges', 'badge_id', 'user_id')
                    ->withPivot('earned_at');
    }
}