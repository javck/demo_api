<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTag extends Model
{
    protected $table = 'post_tag';
    use HasFactory;

    public function scopeContain($query, $post_id, $tag_id)
    {
        return $query->where('post_id', $post_id)->where('tag_id', $tag_id);
    }
}
