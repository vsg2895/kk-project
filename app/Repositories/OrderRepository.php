<?php namespace Jakten\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Jakten\Helpers\Search;
use Jakten\Models\{Order, Organization, User};
use Jakten\Repositories\Contracts\OrderRepositoryContract;

/**
 * Class OrderRepository
 * @package Jakten\Repositories
 */
class OrderRepository extends BaseRepository implements OrderRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return Order::class;
    }

    /**
     * @param User $user
     *
     * @return \Illuminate\Database\Eloquent\Builder|OrderRepositoryContract
     */
    public function byUser(User $user)
    {
        $this->query()->where('user_id', $user->id);

        return $this;
    }

    /**
     * @param Organization $organization
     *
     * @return \Illuminate\Database\Eloquent\Builder|OrderRepositoryContract
     */
    public function forOrganization(Organization $organization)
    {
        $this->query()->whereIn('school_id', $organization->schools->pluck('id')->all());

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderRepositoryContract
     */
    public function notPaid()
    {
        $this->query()->where('paid', false);

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderRepositoryContract
     */
    public function paid()
    {
        $this->query()->where('paid', true);

        return $this;
    }

    /**
     * @param string $paymentMethod
     *
     * @return \Illuminate\Database\Eloquent\Builder|OrderRepositoryContract
     */
    public function withPaymentMethod($paymentMethod)
    {
        $this->query()->where('payment_method', $paymentMethod);

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Database\Eloquent\Builder|OrderRepositoryContract
     */
    public function search(Request $request)
    {
        $this->query()->join('users', 'orders.user_id', '=', 'users.id');
        $this->query()->select('orders.*');

        if ($request->has('status')) {
            $statuses = $request->get('status');
            foreach ($statuses as $status) {
                if ($status === 'handled') {
                    $this->query()->orWhere(function($query){
                        $query->where('orders.invoice_sent', false)->where('orders.handled', true);
                    });
                } elseif ($status === 'unhandled') {
                    $this->query()->orWhere('orders.handled', false);
                } elseif ($status === 'invoice_sent') {
                    $this->query()->orWhere('orders.invoice_sent', true);
                }
            }
        }

        if ($request->has('sort')) {
            $sort = $request->get('sort');

            if ($sort === 'order_status') {
                $this->query()
                    ->orderBy('invoice_sent')
                    ->orderBy('handled', 'desc')
                    ->orderBy('orders.school_id');
            } elseif($sort === 'order_created') {
                $this->query()
                    ->orderBy('orders.created_at', 'desc');
            }
        } else {
            $this->query()->orderBy('invoice_sent')
                ->orderBy('handled', 'desc')
                ->orderBy('orders.school_id');
        }

        return Search::search($request->get('s'), $this->query(), 'orders', function($queryBuilder, $terms) {
            $queryBuilder->join('schools', 'orders.school_id', '=', 'schools.id');
            $queryBuilder->leftJoin('order_items', 'order_items.order_id', '=', 'orders.id');
            $queryBuilder->leftJoin('course_participants', 'order_items.id', '=', 'course_participants.order_item_id');
            $queryBuilder->leftJoin('addon_participants', 'order_items.id', '=', 'addon_participants.order_item_id');
            $queryBuilder->whereNull('schools.deleted_at')->distinct();

            $queryBuilder->where( function($query) use ($terms) {
                foreach ($terms as $str) {
                    foreach ([
                        'schools.name',
                        'schools.contact_email',
                        'users.email',
                        'course_participants.email',
                        'addon_participants.email',
                    ] as $field) {
                        $query->orWhere($field, 'like', '%' . $str . '%');
                    }
                }
            });
        }, true);
    }

    /**
     * @return $this
     */
    public function getKlarnaExpiresOrders()
    {
        $this->query()
            ->where('klarna_expires_at', '>', Carbon::now())
            ->where('klarna_expires_at', '<', Carbon::now()->addDay(2))
            ->where('handled', '=', FALSE)
            ->where('cancelled', '=', FALSE);
        return $this;
    }
}
