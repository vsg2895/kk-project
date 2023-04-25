<?php namespace Jakten\Providers;

use Illuminate\Support\ServiceProvider;
use Jakten\Observers\{ContactRequestObserver,
    OrderItemObserver,
    OrderObserver,
    PendingOrderObserver,
    PostMultimediaObserver,
    PostObserver,
    SchoolObserver,
    SchoolRatingObserver,
    UserObserver};
use Jakten\Models\{ContactRequest,
    Order,
    OrderItem,
    PendingExternalOrder,
    Post,
    PostMultimedia,
    School,
    SchoolRating,
    User};

/**
 * Class ObserverServiceProvider
 * @package Jakten\Providers
 */
class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        SchoolRating::observe(SchoolRatingObserver::class);
        ContactRequest::observe(ContactRequestObserver::class);
        User::observe(UserObserver::class);
        Order::observe(OrderObserver::class);
        OrderItem::observe(OrderItemObserver::class);
        PendingExternalOrder::observe(PendingOrderObserver::class);
        School::observe(SchoolObserver::class);
        Post::observe(PostObserver::class);
        PostMultimedia::observe(PostMultimediaObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
