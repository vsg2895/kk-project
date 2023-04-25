<?php namespace Jakten\Repositories;

use Jakten\Models\PageUri;
use Jakten\Repositories\Contracts\PageUriRepositoryContract;

/**
 * Eloquent uri repository.
 *
 * @author Nectima <dev@nectima.se>
 */
class PageUriRepository extends BaseRepository implements PageUriRepositoryContract
{
    /**
     * @return mixed|string
     */
    protected function model()
    {
        return PageUri::class;
    }

    /**
     * Find by uri
     *
     * @param $uri
     * @param bool $onlyIsActive
     *
     * @return \Illuminate\Database\Eloquent\Builder|PageUriRepositoryContract
     */
    public function byUri($uri, $onlyIsActive = false)
    {
        $uri = $this->addLeadingSlash($uri);
       
        $this->query()->where('uri', $uri);

        if ($onlyIsActive) {
            $this->query()->whereIn('status', [PageUri::ACTIVE, PageUri::REDIRECT]);
        }

        return $this;
    }

    /**
     * @param $uri
     * @return string
     */
    protected function addLeadingSlash($uri)
    {
        $slash = mb_substr($uri, 0, 1, 'utf-8');
        if ($slash !== '/') {
            return '/' . $uri;
        }

        return $uri;
    }
}
