<?php

namespace App\Repositories;

use App\Models\Comment;

class CommentRepository
{
    /**
     * Create a new comment.
     *
     * @param array $data
     * @return Comment
    */
    public function create(array $data): Comment
    {
        return Comment::create($data);
    }

}