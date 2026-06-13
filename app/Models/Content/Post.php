<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Auth\User;
use App\Models\History\PostEditHistory;
use App\Models\Interaction\Bookmark;
use App\Models\Interaction\Vote;

class Post extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $table = 'posts';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'body',
        'status',
        'view_count',
        'vote_score',
        'is_answered',
        'accepted_answer_id'
    ];

    protected $appends = ['user_vote', 'is_liked', 'likes_count', 'is_bookmarked'];

    protected $casts = [
        'view_count' => 'integer',
        'vote_score' => 'integer',
        'is_answered' => 'boolean',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tags', 'post_id', 'tag_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function acceptedAnswer(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'accepted_answer_id');
    }

    public function editHistories(): HasMany
    {
        return $this->hasMany(PostEditHistory::class, 'post_id');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class, 'post_id');
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

    public function getIsBookmarkedAttribute(): bool
    {
        $user = auth('sanctum')->user();
        if (!$user) return false;

        if ($this->relationLoaded('bookmarks')) {
            return $this->bookmarks->contains('user_id', $user->id);
        }

        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }

    public function votes()
    {   
        return $this->hasMany(Vote::class, 'target_id')->where('target_type', 'post');
    }

    public function likes()
    {
        return $this->hasMany(\App\Models\Interaction\Like::class, 'target_id')->where('target_type', 'post');
    }
}