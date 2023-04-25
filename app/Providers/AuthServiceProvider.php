<?php namespace Jakten\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Jakten\Models\{Course, Order, OrderItem, School, SchoolUsps, SchoolRating, User};
use Jakten\Policies\{CoursePolicy, OrderItemPolicy, OrderPolicy,
    SchoolPolicy, SchoolUspsPolicy, SchoolRatingPolicy, UserPolicy};

/**
 * Class AuthServiceProvider
 * @package Jakten\Providers
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        SchoolRating::class => SchoolRatingPolicy::class,
        OrderItem::class => OrderItemPolicy::class,
        School::class => SchoolPolicy::class,
        Order::class => OrderPolicy::class,
        Course::class => CoursePolicy::class,
        User::class => UserPolicy::class,
        SchoolUsps::class => SchoolUspsPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
