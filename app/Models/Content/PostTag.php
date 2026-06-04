<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PostTag extends Model
{
    use HasUuids;

    protected $table = 'post_tags';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['post_id', 'tag_id'];
}