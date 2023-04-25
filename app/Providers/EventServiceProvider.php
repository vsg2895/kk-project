<?php namespace Jakten\Providers;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Jakten\Events\{BecomeTopPartnerApplication,
    NewOrder,
    OrderRebooked,
    ReviewVerified,
    NewRegistration,
    NewReview,
    OrderCancelled,
    OrderFailed as EventOrderFailed};
use Jakten\Listeners\{CheckSchoolLevel,
    SendAdminNewReview,
    SendNewOrderNotify,
    SendNewRegisterNotify,
    SendOrderFailedNotify,
    SendOrderCancelledNotify,
    SendOrderCancelledConfirmation,
    SendOrderConfirmation,
    SendOrderRebookedConfirmation,
    SendOrderRebookedNotify,
    SendRegistrationConfirmation,
    LogSentMessage,
    SendSchoolNewReview,
    SendSchoolOrderCancelledConfirmation,
    SendSchoolOrderConfirmation,
    CancelKlarnaOrder,
    SendOrderFailedMessage,
    SendSchoolOrderRebookedConfirmation,
    SendTopPartnerApplication};

/**
 * Class EventServiceProvider
 * @package Jakten\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        NewRegistration::class => [
            SendRegistrationConfirmation::class,
            SendNewRegisterNotify::class
        ],
        NewReview::class => [
            SendAdminNewReview::class,
        ],
        ReviewVerified::class => [
            SendSchoolNewReview::class,
        ],
        NewOrder::class => [
            SendOrderConfirmation::class,
            SendSchoolOrderConfirmation::class,
            SendNewOrderNotify::class,
            CheckSchoolLevel::class
        ],
        OrderCancelled::class => [
            SendOrderCancelledConfirmation::class,
            SendSchoolOrderCancelledConfirmation::class,
            SendOrderCancelledNotify::class
        ],
        OrderRebooked::class => [
            SendOrderRebookedConfirmation::class,
            SendSchoolOrderRebookedConfirmation::class,
            SendOrderRebookedNotify::class
        ],
        MessageSent::class => [
            LogSentMessage::class
        ],
        BecomeTopPartnerApplication::class => [
            SendTopPartnerApplication::class
        ],
        EventOrderFailed::class => [
            SendOrderFailedMessage::class,
            CancelKlarnaOrder::class,
            SendOrderFailedNotify::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
