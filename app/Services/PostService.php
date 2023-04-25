<?php namespace Jakten\Services;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Jakten\Facades\Auth;
use Jakten\Models\Post;
use Jakten\Repositories\Contracts\PostRepositoryContract;
use Jakten\Services\Asset\Strategy\ImageAbstract;
use Jakten\Services\Asset\Strategy\ImagePreview;

/**
 * Class PostService
 *
 * @package Jakten\Services
 */
class PostService
{
    /**
     * @var PostRepositoryContract
     */
    private $posts;

    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * @var PostMultimediaService
     */
    private $multimediaService;

    /**
     * PostService constructor.
     *
     * @param PostRepositoryContract $posts
     * @param ModelService           $modelService
     * @param PostMultimediaService  $postMultimediaService
     */
    public function __construct(
        PostRepositoryContract $posts,
        ModelService $modelService,
        PostMultimediaService $postMultimediaService
    ) {
        $this->posts = $posts;
        $this->modelService = $modelService;
        $this->multimediaService = $postMultimediaService;
    }

    /**
     * @param FormRequest $request
     * @return Post
     */
    public function storePost(FormRequest $request)
    {
        $previewImgFilename = $request->file('preview_img_filename');

        $data = $request->except(['preview_img_filename', 'image', 'video', 'alt_text']);
        $data['user_id'] = Auth::user()->id;


        if ($previewImgFilename) {
            $previewImagePath = $this->storePreviewImage(new ImagePreview($previewImgFilename));
            $data['preview_img_filename'] = 'public/upload/' . $previewImagePath;
        }

        /** @var Post $post */
        $post = $this->modelService->createModel(Post::class, $data);

        $post->save();

        if ($request->has('image') || $request->has('video')) {
            $data = $request->only(['image', 'video', 'alt_text']);

            $this->multimediaService
                ->storeMultimedia($post, $data);
        }

        return $post;
    }

    /**
     * @param Post        $post
     * @param FormRequest $request
     * @return Post
     * @throws \Exception
     */
    public function updatePost(Post $post, FormRequest $request)
    {
        $postData = $request->except([
            'preview_img_filename',
            'image',
            'video',
            'alt_text',
            'multimedia_delete',
            'preview_img_filename_delete',
        ]);

        if ($request->has('preview_img_filename') || $request->has('preview_img_filename_delete')) {
            if (!is_null($post->preview_img_filename)) {
                Storage::delete($post->preview_img_filename);
            }
            $previewImgFilename = $request->file('preview_img_filename', null);

            if ($request->has('preview_img_filename')) {
                $previewImagePath = $this->storePreviewImage(new ImagePreview($previewImgFilename));
                $previewImgFilename = 'public/upload/' . $previewImagePath;
            }

            $postData['preview_img_filename'] = $previewImgFilename;
        }

        /** @var Post $post */
        $post = $this->modelService->updateModel($post, $postData);

        $post->save();

        if ($request->has('image') || $request->has('video') || $request->has('multimedia_delete') || $request->has('alt_text')) {
            $data = $request->only([
                'image',
                'video',
                'alt_text',
                'multimedia_delete',
            ]);

            $multimedia = $post->multimedia->first();
            if (is_null($multimedia)) {
                $this->multimediaService
                    ->storeMultimedia($post, $data);
            } elseif ($request->has('multimedia_delete')) {
                $this->multimediaService
                    ->deleteMultimedia($multimedia);
            } else {
                $this->multimediaService
                    ->updateMultimedia($multimedia, $data);
            }
        }

        return $post;
    }

    /**
     * @param Post $post
     * @throws \Exception
     */
    public function deletePost(Post $post)
    {
        $post->delete();
    }

    /**
     * @param Request $request
     * @param int     $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search(Request $request, $limit = 20)
    {
        $query = $this->posts;

        if (Auth::guest() || !Auth::user()->isAdmin()) {
            $query = $query->isVisible();
        }

        $query = $query->query()->orderBy('created_at', 'desc');

        return $query
            ->select([
                'posts.id',
                'posts.title',
                'posts.slug',
                'posts.content',
                'posts.status',
                'posts.hidden',
                'posts.preview_img_filename',
                'posts.created_at',
            ])
            ->paginate($limit);
    }

    /**
     * Stores an image in the public upload folder.
     *
     * @param ImageAbstract $strategy
     * @return false|string
     */
    public function storePreviewImage(ImageAbstract $strategy)
    {
        $strategy->image->save($strategy->filePath);

        return $strategy->path;
    }
}
