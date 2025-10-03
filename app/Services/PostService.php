<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PostService
{
    public function __construct(
        private readonly PostRepository $postRepository
    ) {}

    /**
     * Get all posts with relations.
     *
     * @return Collection
    */
    public function getAllPosts(): Collection
    {
        return Cache::remember('posts.all', now()->addMinutes(10), function () {
            return $this->postRepository->getAllWithRelations();
        });
    }

    /**
     * Get a post by ID with relations.
     *
     * @param int $id
     * @return Post|null
    */
    public function getPostById(int $id): ?Post
    {
        return Cache::remember("posts.{$id}", now()->addMinutes(10), function () use ($id) {
            return $this->postRepository->findWithRelations($id);
        });
    }

    /**
     * Create a new post.
     *
     * @param array $data
     * @return Post
    */
    public function createPost(array $data): Post
    {
        return DB::transaction(function () use ($data) {
            $post = $this->postRepository->create($data);
            
            Cache::forget('posts.all');
            
            return $post->load('user:id,name', 'category:id,name');
        });
    }

    /**
     * Update a post.
     *
     * @param int $id
     * @param array $data
     * @return Post|null
    */
    public function updatePost(int $id, array $data): ?Post
    {
        return DB::transaction(function () use ($id, $data) {
            $post = $this->postRepository->update($id, $data);
            
            if ($post) {
                Cache::forget('posts.all');
                Cache::forget("posts.{$id}");
            }
            
            return $post;
        });
    }

    /**
     * Delete a post.
     *
     * @param int $id
     * @return bool
    */
    public function deletePost(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $deleted = $this->postRepository->delete($id);
            
            if ($deleted) {
                Cache::forget('posts.all');
                Cache::forget("posts.{$id}");
            }
            
            return $deleted;
        });
    }

    /**
     * Get posts by category ID.
     *
     * @param int $categoryId
     * @return Collection
    */
    public function getPostsByCategory(int $categoryId): Collection
    {
        return Cache::remember("posts.category.{$categoryId}", now()->addMinutes(10), function () use ($categoryId) {
            return $this->postRepository->getByCategoryWithRelations($categoryId);
        });
    }
    
}