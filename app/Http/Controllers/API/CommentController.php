<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreCommentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Traits\ApiResponses;
use App\Services\CommentService;
use App\Http\Resources\API\CommentResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class CommentController extends Controller
{
    use ApiResponses, AuthorizesRequests;

    public function __construct(
        private readonly CommentService $commentService
    ) {}
    

    /**
     * Store a newly created comment.
     *
     * @param StoreCommentRequest $request
     * @return JsonResponse
    */
    public function store(StoreCommentRequest $request, int $postId): JsonResponse
    {
        try {
            $this->setLocale($request->header('language'));

            $comment = $this->commentService->createComment([
                'content' => $request->content,
                'user_id' => auth()->id(),
                'post_id' => $postId,
            ]);

            $this->authorize('create', $comment);

            return $this->successResponse([
                'statusCode' => Response::HTTP_CREATED,
                'message' => Lang::get('messages.comment_created_successfully'),
                'data' => new CommentResource($comment),
            ], Response::HTTP_CREATED);

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
