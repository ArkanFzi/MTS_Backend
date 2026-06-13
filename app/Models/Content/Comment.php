<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Auth\User;
use App\Models\History\CommentEditHistory;
use App\Models\Interaction\Vote;

class Comment extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $table = 'comments';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'body',
        'vote_score',
        'is_accepted'
    ];

    protected $appends = ['user_vote', 'is_liked', 'likes_count'];

    protected $casts = [
        'vote_score' => 'integer',
        'is_accepted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function getUserVoteAttribute(): ?string
    {
        $user = auth('sanctum')->user();
        if (!$user) return null;

        // Fetch vote if it was loaded or query it
        if ($this->relationLoaded('votes')) {
            $vote = $this->votes->firstWhere('user_id', $user->id);
        } else {
            $vote = $this->votes()->where('user_id', $user->id)->first();
        }

        if (!$vote) return null;
        return (int)$vote->vote_type === 1 ? 'up' : 'down';
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function editHistories(): HasMany
    {
        return $this->hasMany(CommentEditHistory::class, 'comment_id');
    }

    public function getIsLikedAttribute(): bool
    {
        $user = auth('sanctum')->user();
        if (!$user) return false;

        if ($this->relationLoaded('likes')) {
            return $this->likes->contains('user_id', $user->id);
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function getLikesCountAttribute(): int
    {
        if ($this->relationLoaded('likes')) {
            return $this->likes->count();
        }
        return $this->likes()->count();
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'target_id')->where('target_type', 'comment');
    }

    public function likes()
    {
        return $this->hasMany(\App\Models\Interaction\Like::class, 'target_id')->where('target_type', 'comment');
    }
}