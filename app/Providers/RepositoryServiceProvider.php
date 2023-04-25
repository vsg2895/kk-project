<?php namespace Jakten\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Jakten\Repositories\AnnotationRepository;
use Jakten\Repositories\AssetRepository;
use Jakten\Repositories\CityInfoRepository;
use Jakten\Repositories\CityRepository;
use Jakten\Repositories\CommentRepository;
use Jakten\Repositories\ContactRequestRepository;
use Jakten\Repositories\Contracts\AnnotationRepositoryContract;
use Jakten\Repositories\Contracts\AssetRepositoryContract;
use Jakten\Repositories\Contracts\CityInfoRepositoryContract;
use Jakten\Repositories\Contracts\CityRepositoryContract;
use Jakten\Repositories\Contracts\CommentRepositoryContract;
use Jakten\Repositories\Contracts\ContactRequestRepositoryContract;
use Jakten\Repositories\Contracts\CountyRepositoryContract;
use Jakten\Repositories\Contracts\CourseRepositoryContract;
use Jakten\Repositories\Contracts\GiftCardTypeRepositoryContract;
use Jakten\Repositories\Contracts\InvoiceRepositoryContract;
use Jakten\Repositories\Contracts\OrderItemRepositoryContract;
use Jakten\Repositories\Contracts\OrderRepositoryContract;
use Jakten\Repositories\Contracts\OrganizationRepositoryContract;
use Jakten\Repositories\Contracts\PageRepositoryContract;
use Jakten\Repositories\Contracts\PageUriRepositoryContract;
use Jakten\Repositories\Contracts\PartnerRepositoryContract;
use Jakten\Repositories\Contracts\PostMultimediaRepositoryContract;
use Jakten\Repositories\Contracts\PostRepositoryContract;
use Jakten\Repositories\Contracts\RatingRepositoryContract;
use Jakten\Repositories\Contracts\SchoolClaimRepositoryContract;
use Jakten\Repositories\Contracts\SchoolRepositoryContract;
use Jakten\Repositories\Contracts\SchoolSegmentPriceRepositoryContract;
use Jakten\Repositories\Contracts\UserRepositoryContract;
use Jakten\Repositories\Contracts\UspsRepositoryContract;
use Jakten\Repositories\Contracts\VehicleRepositoryContract;
use Jakten\Repositories\Contracts\VehicleSegmentRepositoryContract;
use Jakten\Repositories\CountyRepository;
use Jakten\Repositories\CourseRepository;
use Jakten\Repositories\GiftCardTypeRepository;
use Jakten\Repositories\InvoiceRepository;
use Jakten\Repositories\OrderItemRepository;
use Jakten\Repositories\OrderRepository;
use Jakten\Repositories\OrganizationRepository;
use Jakten\Repositories\PageRepository;
use Jakten\Repositories\PageUriRepository;
use Jakten\Repositories\PartnerRepository;
use Jakten\Repositories\PostMultimediaRepository;
use Jakten\Repositories\PostRepository;
use Jakten\Repositories\RatingRepository;
use Jakten\Repositories\SchoolClaimRepository;
use Jakten\Repositories\SchoolRepository;
use Jakten\Repositories\SchoolSegmentPriceRepository;
use Jakten\Repositories\UserRepository;
use Jakten\Repositories\UspsRepository;
use Jakten\Repositories\VehicleRepository;
use Jakten\Repositories\VehicleSegmentRepository;

/**
 * Class RepositoryServiceProvider
 */
class RepositoryServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    private $contracts = [
        OrderItemRepositoryContract::class => OrderItemRepository::class,
        CityRepositoryContract::class => CityRepository::class,
        CountyRepositoryContract::class => CountyRepository::class,
        CourseRepositoryContract::class => CourseRepository::class,
        SchoolSegmentPriceRepositoryContract::class => SchoolSegmentPriceRepository::class,
        SchoolRepositoryContract::class => SchoolRepository::class,
        UserRepositoryContract::class => UserRepository::class,
        RatingRepositoryContract::class => RatingRepository::class,
        OrganizationRepositoryContract::class => OrganizationRepository::class,
        VehicleSegmentRepositoryContract::class => VehicleSegmentRepository::class,
        VehicleRepositoryContract::class => VehicleRepository::class,
        OrderRepositoryContract::class => OrderRepository::class,
        InvoiceRepositoryContract::class => InvoiceRepository::class,
        PageRepositoryContract::class => PageRepository::class,
        PageUriRepositoryContract::class => PageUriRepository::class,
        ContactRequestRepositoryContract::class => ContactRequestRepository::class,
        SchoolClaimRepositoryContract::class => SchoolClaimRepository::class,
        AssetRepositoryContract::class => AssetRepository::class,
        AnnotationRepositoryContract::class => AnnotationRepository::class,
        UspsRepositoryContract::class => UspsRepository::class,
        GiftCardTypeRepositoryContract::class => GiftCardTypeRepository::class,
        CityInfoRepositoryContract::class => CityInfoRepository::class,
        PostRepositoryContract::class => PostRepository::class,
        CommentRepositoryContract::class => CommentRepository::class,
        PostMultimediaRepositoryContract::class => PostMultimediaRepository::class,
        PartnerRepositoryContract::class => PartnerRepository::class,
    ];

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->contracts as $contract => $repository) {
            $this->app->singleton($contract, $repository);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_keys($this->contracts);
    }
}
