<?php namespace Blog\Http\Controllers;

use Api\Http\Controllers\ApiController;
use Blog\Http\Requests\StorePostRequest;
use Blog\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;
use Jakten\Models\Post;
use Jakten\Repositories\Contracts\PostRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\PostService;

class PostController extends ApiController
{
    /**
     * @var PostRepositoryContract
     */
    protected $posts;

    /**
     * @var PostService
     */
    private $postService;

    /**
     * PostController constructor.
     *
     * @param PostRepositoryContract $posts
     * @param PostService $postService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(PostRepositoryContract $posts, PostService $postService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->posts = $posts;
        $this->postService = $postService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('blog::posts.index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexAdmin()
    {
        $posts = $this->posts->query()->whereHas('user')->sortable([
            'created_at' => 'desc'
        ]);

        return view('blog::posts.index-admin', [
            'posts' => $posts->where('post_type', $this->posts->getPostTypes()['post'])->paginate(15),
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        if ($request->landing) {
            return view('blog::landing_pages.create');
        }
        return view('blog::posts.create');
    }

    /**
     * @param StorePostRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePostRequest $request)
    {
        $post = $this->postService
            ->storePost($request);

        return $this->success([
            'post' => $post->id,
        ]);
    }

    /**
     * @param Post $post
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, $slug)
    {
        $post = $this->posts->query()->where('slug', $slug)->firstOrFail();

        $multimedia = $post->multimedia->first();

        return view('blog::posts.show')
            ->with('post', $post)
            ->with('multimedia', $multimedia);
    }

    public function landingShow(Request $request, $slug)
    {
        $post = $this->posts->query()->where('slug', $slug)->firstOrFail();

        return view('blog::posts.landing_show')
            ->with('post', $post);
    }

    /**
     * @param Post $post
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Post $post, Request $request)
    {
        if ($request->landing) {
            return view('blog::landing_pages.edit')->with('post', $post);
        }
        return view('blog::posts.edit')
            ->with('post', $post);
    }

    /**
     * @param Post              $post
     * @param UpdatePostRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Post $post, UpdatePostRequest $request)
    {
        $post = $this->postService
            ->updatePost($post, $request);

        return $this->success([
            'post' => $post->id,
        ]);
    }

    /**
     * @param Post $post
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete(Post $post)
    {
        $this->postService
            ->deletePost($post);

        return back(303);
    }
}
