<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class PostRepository
{
    /**
     * Get all posts with relations.
     *
     * @return Collection
     */
    public function getAllWithRelations(): Collection
    {
        return Post::query()
            ->with([
                'user:id,name',
                'category:id,name'
            ])
            ->withCount('comments')
            ->latest()
            ->get();
    }

    /**
     * Find a post by ID with relations.
     *
     * @param int $id
     * @return Post|null
     */
    public function findWithRelations(int $id): ?Post
    {
        return Post::query()
            ->with([
                'user:id,name',
                'category:id,name',
                'comments',
            ])
            ->find($id);
    }

    /**
     * Create a new post.
     *
     * @param array $data
     * @return Post
    */
    public function create(array $data): Post
    {
        return Post::create($data);
    }

    /**
     * Update a post.
     *
     * @param int $id
     * @param array $data
     * @return Post|null
    */
    public function update(int $id, array $data): ?Post
    {
        $post = Post::find($id);

        if (!$post) {
            return null;
        }

        $post->update($data);
        
        return $post->load([
            'user:id,name',
            'category:id,name'
        ]);
    }

    /**
     * Delete a post.
     *
     * @param int $id
     * @return bool
    */
    public function delete(int $id): bool
    {
        $post = Post::find($id);

        if (!$post) {
            return false;
        }

        return $post->delete();
    }

    /**
     * Get posts by category ID.
     *
     * @param int $categoryId
     * @return Collection
    */
    public function getByCategoryWithRelations(int $categoryId): Collection
    {
        return Post::query()
            ->where('category_id', $categoryId)
            ->with([
                'user:id,name',
                'category:id,name'
            ])
            ->withCount('comments')
            ->latest()
            ->get();
    }

}