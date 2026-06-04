<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Auth\User;
use App\Models\History\PostEditHistory;
use App\Models\Interaction\Bookmark;
use App\Models\Interaction\Vote;

class Post extends Model
{
    use HasUuids;

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

    protected $casts = [
        'view_count' => 'integer',
        'vote_score' => 'integer',
        'is_answered' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

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

    public function votes()
    {
        return $this->hasMany(Vote::class, 'target_id')->where('target_type', 'post');
    }
}