<?php namespace Api\Http\Controllers;

use Illuminate\Http\Request;
use Jakten\Facades\Auth;
use Jakten\Models\Post;
use Jakten\Presenters\SearchedComments;
use Jakten\Presenters\SearchedPosts;
use Jakten\Repositories\Contracts\CommentRepositoryContract;
use Jakten\Repositories\Contracts\PostRepositoryContract;
use Jakten\Services\CommentService;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\PostService;

/**
 * Class BlogController
 *
 * @package Api\Http\Controllers
 */
class BlogController extends ApiController
{
    /**
     * @var PostRepositoryContract
     */
    private $posts;

    /**
     * @var CommentRepositoryContract
     */
    private $comments;

    /**
     * @var PostService
     */
    private $postService;

    /**
     * @var CommentService
     */
    private $commentService;

    /**
     * BlogController constructor.
     *
     * @param PostRepositoryContract $posts
     * @param CommentRepositoryContract $comments
     * @param PostService $postService
     * @param CommentService $commentService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(
        PostRepositoryContract $posts,
        CommentRepositoryContract $comments,
        PostService $postService,
        CommentService $commentService,
        KKJTelegramBotService $botService
    )
    {
        parent::__construct($botService);
        $this->posts = $posts;
        $this->comments = $comments;
        $this->postService = $postService;
        $this->commentService = $commentService;
    }

    /**
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function findPost(Post $post)
    {
        if ($post->isVisible() || (!Auth::guest() && Auth::user()->isAdmin())) {
            $presenter = new SearchedPosts();
            $post = $presenter->formatModel($post);
        } else {
            abort(404);
        }

        return $this->success($post);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPosts(Request $request)
    {
        $presenter = new SearchedPosts();
        $limit = $request->input('limit', 20);

        $paginator = $this->postService->search($request, $limit);
        $posts = $presenter->format(collect($paginator->items()));

        return $this->success([
            'posts' => $posts,
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchComments(Request $request)
    {
        $presenter = new SearchedComments();
        $limit = $request->input('limit', 20);

        $paginator = $this->commentService->search($request, $limit);
        $comments = $presenter->format(collect($paginator->items()));

        return $this->success([
            'comments' => $comments,
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ]);
    }
}
