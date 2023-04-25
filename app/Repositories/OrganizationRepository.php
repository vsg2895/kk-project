<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Jakten\Helpers\Search;
use Jakten\Models\Organization;
use Jakten\Repositories\Contracts\OrganizationRepositoryContract;

/**
 * Class OrganizationRepository
 * @package Jakten\Repositories
 */
class OrganizationRepository extends BaseRepository implements OrganizationRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return Organization::class;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function search(Request $request)
    {
        $this->query()
//            ->withTrashed()
            ->leftJoin('users', 'users.organization_id', '=', 'organizations.id')
            ->select('organizations.*')
            ->groupBy('organizations.id');

        return Search::search($request->get('s'), $this->query(), 'organizations', function($queryBuilder, $terms) {
            $queryBuilder->where(function($query) use ($terms) {
                foreach ($terms as $str) {
                    foreach ([
                        'users.given_name',
                        'users.family_name',
                        'users.email',
                        'name',
                        'org_number',
                    ] as $field) {
                        $query->orWhere($field, 'like', '%' . $str . '%');
                    }
                }
            });
        }, true);
    }
}
