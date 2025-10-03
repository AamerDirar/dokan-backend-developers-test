<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StorePostRequest;
use App\Http\Requests\API\UpdatePostRequest;
use App\Http\Resources\API\AllPostsResource;
use App\Http\Resources\API\PostResource;
use App\Http\Traits\ApiResponses;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use ApiResponses, AuthorizesRequests;

    public function __construct(
        private readonly PostService $postService
    ) {}

    /**
     * Display a listing of posts.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $this->setLocale(request()->header('language'));

            $posts = $this->postService->getAllPosts();

            if ($posts->isEmpty()) {
                return $this->errorResponse(
                    [],
                    Lang::get('messages.no_posts_found'),
                    Response::HTTP_NOT_FOUND
                );
            }

            return $this->successResponse([
                'statusCode' => Response::HTTP_OK,
                'message' => Lang::get('messages.posts_retrieved_successfully'),
                'data' => AllPostsResource::collection($posts),
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return $this->handleException($th);
        }
    }

    /**
     * Store a newly created post.
     *
     * @param StorePostRequest $request
     * @return JsonResponse
    */
    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            $this->setLocale($request->header('language'));

            $post = $this->postService->createPost([
                'title' => $request->title,
                'content' => $request->content,
                'user_id' => auth()->id(),
                'category_id' => $request->category_id,
            ]);

            $this->authorize('create', $post);

            return $this->successResponse([
                'statusCode' => Response::HTTP_CREATED,
                'message' => Lang::get('messages.post_created_successfully'),
                'data' => new PostResource($post),
            ], Response::HTTP_CREATED);

        } catch (\Throwable $th) {
            return $this->handleException($th);
        }
    }

    /**
     * Display the specified post.
     *
     * @param int $id
     * @return JsonResponse
    */
    public function show(int $id): JsonResponse
    {
        try {
            $this->setLocale(request()->header('language'));

            $post = $this->postService->getPostById($id);

            if (!$post) {
                return $this->errorResponse(
                    [],
                    Lang::get('messages.no_post_found'),
                    Response::HTTP_NOT_FOUND
                );
            }

            return $this->successResponse([
                'statusCode' => Response::HTTP_OK,
                'message' => Lang::get('messages.post_retrieved_successfully'),
                'data' => new PostResource($post),
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return $this->handleException($th);
        }
    }

     /**
     * Update the specified post.
     *
     * @param UpdatePostRequest $request
     * @param int $id
     * @return JsonResponse
    */
    public function update(UpdatePostRequest $request, int $id): JsonResponse
    {
        try {
            $this->setLocale($request->header('language'));

            $post = $this->postService->updatePost($id, $request->validated());
            
            $this->authorize('update', $post);

            if (!$post) {
                return $this->errorResponse(
                    [],
                    Lang::get('messages.no_post_found'),
                    Response::HTTP_NOT_FOUND
                );
            }

            return $this->successResponse([
                'statusCode' => Response::HTTP_OK,
                'message' => Lang::get('messages.post_updated_successfully'),
                'data' => new PostResource($post),
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return $this->handleException($th);
        }
    }

    /**
     * Remove the specified post.
     *
     * @param int $id
     * @return JsonResponse
    */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->setLocale();
            
            $post = $this->postService->getPostById($id);
            if (!$post) {
                return $this->errorResponse(
                    [],
                    Lang::get('messages.no_post_found'),
                    Response::HTTP_NOT_FOUND
                );
            }
            $this->authorize('delete', $post);

            $deleted = $this->postService->deletePost($id);
            

            if (!$deleted) {
                return $this->errorResponse(
                    [],
                    Lang::get('messages.no_post_found'),
                    Response::HTTP_NOT_FOUND
                );
            }

            return $this->successResponse([
                'statusCode' => Response::HTTP_OK,
                'message' => Lang::get('messages.post_deleted_successfully'),
                'data' => null,
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return $this->handleException($th);
        }
    }

    /**
     * List posts by category.
     *
     * @param int $categoryId
     * @return JsonResponse
    */
    public function postsByCategory(int $categoryId): JsonResponse
    {
        try {
            $this->setLocale(request()->header('language'));                
            $posts = $this->postService->getPostsByCategory($categoryId);
            if ($posts->isEmpty()) {
                return $this->errorResponse(
                    [],
                    Lang::get('messages.no_posts_found_in_category'),
                    Response::HTTP_NOT_FOUND
                );
            }   

            return $this->successResponse([
                'statusCode' => Response::HTTP_OK,
                'message' => Lang::get('messages.posts_retrieved_successfully'),
                'data' => AllPostsResource::collection($posts),
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->handleException($th);

        }
    }

    /**
     * Set application locale based on request header.
     *
     * @param string|null $language
     * @return void
    */
    private function setLocale(?string $language = null): void
    {
        $lang = $language ?? request()->header('language', 'ar');
        App::setLocale($lang);
    }

    /**
     * Handle exceptions and return appropriate error response.
     *
     * @param \Throwable $exception
     * @return JsonResponse
    */
    private function handleException(\Throwable $exception): JsonResponse
    {
        report($exception);

        return $this->errorResponse(
            [],
            config('app.debug') ? $exception->getMessage() : Lang::get('messages.something_went_wrong'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}