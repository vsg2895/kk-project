<?php namespace Jakten\Services;

use Illuminate\Support\Facades\Storage;
use Jakten\Models\Post;
use Jakten\Models\PostMultimedia;
use Jakten\Repositories\Contracts\PostMultimediaRepositoryContract;
use Jakten\Services\Asset\Strategy\ImageAbstract;
use Jakten\Services\Asset\Strategy\ImagePost;

/**
 * Class PostMultimediaService
 *
 * @package Jakten\Services
 */
class PostMultimediaService
{
    /**
     * @var PostMultimediaRepositoryContract
     */
    private $postMultimedia;

    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * PostMultimediaService constructor.
     *
     * @param PostMultimediaRepositoryContract $postMultimedia
     * @param ModelService                     $modelService
     */
    public function __construct(PostMultimediaRepositoryContract $postMultimedia, ModelService $modelService)
    {
        $this->postMultimedia = $postMultimedia;
        $this->modelService = $modelService;
    }

    /**
     * @param string $path
     * @return int
     */
    private function getVideoType(string $path)
    {
        $isYouTube = preg_match('/^(https?\:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/', $path);

        return 7 + (int)$isYouTube;
    }

    /**
     * @param string $url
     * @return string
     */
    private function getYoutubeEmbedUrl(string $url)
    {
        $youtubeId = '';
        $regex = '#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#';

        if (preg_match($regex, $url, $matches)) {
            $youtubeId = array_first($matches);
        }

        return 'https://www.youtube.com/embed/' . $youtubeId;
    }

    /**
     * @param Post  $post
     * @param array $data
     * @return PostMultimedia
     */
    public function storeMultimedia(Post $post, array $data)
    {
        if (array_key_exists('alt_text', $data) && !empty($data['alt_text'])) {
            $altText = $data['alt_text'];
        } else {
            $altText = '';
        }

        if (array_key_exists('image', $data)) {
            $img = $this->storeImage(new ImagePost($data['image']));
            $path = 'public/upload/' . $img->path;
            $type = $img->getType();
        } elseif (array_key_exists('video', $data)) {
            $path = $data['video'];
            $type = $this->getVideoType($path);
            if ($type === 8) {
                $path = $this->getYoutubeEmbedUrl($path);
            }
        }

        /** @var PostMultimedia $multimedia */
        $multimedia = $this->modelService->createModel(PostMultimedia::class, [
            'post_id'  => $post->id,
            'path'     => $path,
            'alt_text' => $altText,
            'type'     => $type,
        ]);

        $multimedia->save();

        return $multimedia;
    }

    /**
     * @param ImageAbstract $strategy
     * @return ImageAbstract
     */
    public function storeImage(ImageAbstract $strategy)
    {
        $strategy->image->save($strategy->filePath);

        return $strategy;
    }

    /**
     * @param PostMultimedia $multimedia
     * @param array          $data
     * @return PostMultimedia
     */
    public function updateMultimedia(PostMultimedia $multimedia, array $data)
    {
        if (array_key_exists('alt_text', $data) && !empty($data['alt_text'])) {
            $altText = $data['alt_text'];
        } else {
            $altText = '';
        }

        if (array_key_exists('image', $data)) {
            $img = $this->storeImage(new ImagePost($data['image']));
            $path = 'public/upload/' . $img->path;
            $type = $img->getType();
        } elseif (array_key_exists('video', $data)) {
            $path = $data['video'];
            $type = $this->getVideoType($path);
            if ($type === 8) {
                $path = $this->getYoutubeEmbedUrl($path);
            }
        }

        if (isset($path)) {
            $update = [
                'path'     => $path,
                'alt_text' => $altText,
                'type'     => $type,
            ];
        } else {
            $update = [
                'alt_text' => $altText,
            ];
        }

        /** @var PostMultimedia $multimedia */
        $multimedia = $this->modelService->updateModel($multimedia, $update);

        $multimedia->save();

        return $multimedia;
    }

    /**
     * @param PostMultimedia $multimedia
     * @throws \Exception
     */
    public function deleteMultimedia(PostMultimedia $multimedia)
    {
        $multimedia->delete();
    }
}
