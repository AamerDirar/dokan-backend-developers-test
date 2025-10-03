<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->category = Category::factory()->create();
});

it('allows an authenticated user to create a post', function () {
    $payload = [
        'title' => 'My Test Post',
        'content' => 'This is a test post content',
        'category_id' => $this->category->id,
    ];

    $response = $this->actingAs($this->user, 'sanctum')
                     ->postJson('/api/posts', $payload);

    $response->assertStatus(201)
             ->assertJsonFragment([
                 'title' => 'My Test Post',
                 'content' => 'This is a test post content',
             ]);

    $this->assertDatabaseHas('posts', [
        'title' => 'My Test Post',
        'user_id' => $this->user->id,
    ]);
});

it('prevents unauthenticated users from creating a post', function () {
    $payload = [
        'title' => 'Unauthorized Post',
        'content' => 'Should not be created',
        'category_id' => $this->category->id,
    ];

    $response = $this->postJson('/api/posts', $payload);

    $response->assertStatus(401);
});

it('allows an authenticated user to post a comment', function () {
    $post = Post::factory()->create(['category_id' => $this->category->id]);

    $payload = ['content' => 'This is a test comment'];

    $response = $this->actingAs($this->user, 'sanctum')
                     ->postJson("/api/posts/{$post->id}/comments", $payload);

    $response->assertStatus(201)
             ->assertJsonFragment([
                 'content' => 'This is a test comment',
                 'post_id' => $post->id,
                 'user_id' => $this->user->id,
             ]);

    $this->assertDatabaseHas('comments', [
        'content' => 'This is a test comment',
        'post_id' => $post->id,
        'user_id' => $this->user->id,
    ]);
});

it('can view a post with all its comments', function () {
    $post = Post::factory()->create(['category_id' => $this->category->id]);
    Comment::factory(3)->create(['post_id' => $post->id]);

    $response = $this->getJson("/api/posts/{$post->id}");

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'id',
                 'title',
                 'content',
                 'user',
                 'category',
                 'comments' => [
                     '*' => ['id', 'content', 'user', 'post_id', 'user_id']
                 ],
             ]);
});
