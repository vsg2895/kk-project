<?php namespace Jakten\Services;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Jakten\Facades\Auth;
use Jakten\Models\Comment;
use Jakten\Repositories\Contracts\CommentRepositoryContract;

/**
 * Class CommentService
 *
 * @package Jakten\Services
 */
class CommentService
{
    /**
     * @var CommentRepositoryContract
     */
    private $comments;

    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * CommentService constructor.
     *
     * @param CommentRepositoryContract $comments
     * @param ModelService              $modelService
     */
    public function __construct(CommentRepositoryContract $comments, ModelService $modelService)
    {
        $this->comments = $comments;
        $this->modelService = $modelService;
    }

    /**
     * @param FormRequest $request
     * @return Comment
     */
    public function storeComment(FormRequest $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::user()->id;

        /** @var Comment $comment */
        $comment = $this->modelService->createModel(Comment::class, $data);

        $comment->save();

        return $comment;
    }

    /**
     * @param Comment     $comment
     * @param FormRequest $request
     * @return Comment
     */
    public function updateComment(Comment $comment, FormRequest $request)
    {
        /** @var Comment $comment */
        $comment = $this->modelService->updateModel($comment, $request->all());
        $comment->save();

        return $comment;
    }

    /**
     * @param Comment $comment
     * @throws \Exception
     */
    public function deleteComment(Comment $comment)
    {
        $comment->delete();
    }

    /**
     * @param Request $request
     * @param int     $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search(Request $request, $limit = 20)
    {
        $postId = $request->input('post_id');

        $query = $this->comments;

        if ($postId) {
            $query->wherePostId($postId);
        }

        $query = $query->query()->orderBy('created_at', 'desc');

        return $query
            ->select([
                'comments.*',
            ])
            ->with(['user'])
            ->paginate($limit);
    }
}
