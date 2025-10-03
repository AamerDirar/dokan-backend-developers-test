<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Comment;
use App\Repositories\CommentRepository;

class CommentService
{
    public function __construct(
        private readonly CommentRepository $commentRepository
    ) {}

    /**
     * Create a new comment.
     *
     * @param array $data
     * @return Comment
    */
    public function createComment(array $data): Comment
    {
        return DB::transaction(function () use ($data) {
            $comment = $this->commentRepository->create($data);
            
            Cache::forget('comments.all');

            return $comment->load('user:id,name', 'post:id,title');
        });
    }
}