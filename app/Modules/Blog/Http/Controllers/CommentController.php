<?php namespace Blog\Http\Controllers;

use Api\Http\Controllers\ApiController;
use Blog\Http\Requests\StoreCommentRequest;
use Blog\Http\Requests\UpdateCommentRequest;
use Jakten\Models\Comment;
use Jakten\Repositories\Contracts\CommentRepositoryContract;
use Jakten\Services\CommentService;
use Jakten\Services\KKJTelegramBotService;

class CommentController extends ApiController
{
    /**
     * @var CommentRepositoryContract
     */
    protected $comments;

    /**
     * @var CommentService
     */
    private $commentService;

    /**
     * CommentController constructor.
     *
     * @param CommentRepositoryContract $comments
     * @param CommentService $commentService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(CommentRepositoryContract $comments, CommentService $commentService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->comments = $comments;
        $this->commentService = $commentService;
    }

    /**
     * @param StoreCommentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCommentRequest $request)
    {
        $comment = $this->commentService
            ->storeComment($request);

        return redirect()
            ->route('api::blog.comments.search', [
                'post_id' => $comment->post_id,
            ]);
    }

    /**
     * @param UpdateCommentRequest $request
     * @param Comment              $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $this->commentService
            ->updateComment($comment, $request);

        return $this->success();
    }

    /**
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(Comment $comment)
    {
        $this->commentService
            ->deleteComment($comment);

        return $this->success();
    }
}
