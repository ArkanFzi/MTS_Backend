<?php

namespace App\Models\History;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Content\Post;
use App\Models\Auth\User;

class PostEditHistory extends Model
{
    use HasUuids;

    protected $table = 'post_edit_history';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['post_id', 'edited_by', 'body_before', 'body_after', 'reason', 'edited_at'];

    protected $casts = [
        'edited_at' => 'datetime'
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}