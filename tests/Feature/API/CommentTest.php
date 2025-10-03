<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->category = Category::factory()->create();
    $this->post = Post::factory()->for($this->user)->for($this->category)->create();
});

it('allows an authenticated user to post a comment', function () {
    $payload = [
        'content' => 'This is a test comment.',
    ];

    $response = $this->actingAs($this->user, 'sanctum')
                     ->postJson("/api/posts/{$this->post->id}/comments", $payload);

    $response->assertStatus(201)
             ->assertJsonFragment([
                 'content' => 'This is a test comment.',
                 'post_id' => $this->post->id,
                 'user_id' => $this->user->id,
             ]);

    $this->assertDatabaseHas('comments', [
        'content' => 'This is a test comment.',
        'post_id' => $this->post->id,
        'user_id' => $this->user->id,
    ]);
});

it('fails to post a comment when unauthenticated', function () {
    $payload = [
        'content' => 'This is a test comment.',
    ];

    $response = $this->postJson("/api/posts/{$this->post->id}/comments", $payload);

    $response->assertStatus(401)
             ->assertJson([
                 'message' => 'Unauthenticated.',
             ]);
});

it('allows viewing a post with all its comments', function () {
    $response = $this->getJson("/api/posts/{$this->post->id}");

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'id',
                 'title',
                 'content',
                 'user' => ['id', 'name'],
                 'category' => ['id', 'name'],
                 'comments' => [
                     '*' => [
                         'id',
                         'content',
                         'user' => ['id', 'name'],
                         'post_id',
                         'user_id'
                     ]
                 ]
             ]);

    $json = $response->json();
    expect(count($json['comments']))->toBe(3);
});
