<?php namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Jakten\Repositories\Contracts\OrganizationRepositoryContract;
use Jakten\Services\Payment\Klarna\KlarnaService;

/**
 * Class CancelReservation
 * @package Jakten\Console\Commands
 */
class CancelReservation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kkj:cancel_reservation {organizationId} {reservationId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel reservation by organization and reservation ID';

    /**
     * @var OrganizationRepositoryContract
     */
    private $organizations;

    /**
     * @var KlarnaService
     */
    private $klarnaService;

    /**
     * Create a new command instance.
     *
     * @param OrganizationRepositoryContract $organizations
     * @param KlarnaService $klarnaService
     */
    public function __construct(OrganizationRepositoryContract $organizations, KlarnaService $klarnaService)
    {
        parent::__construct();
        $this->organizations = $organizations;
        $this->klarnaService = $klarnaService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \KlarnaException
     */
    public function handle()
    {
        $organizationId = $this->argument('organizationId');
        $reservationId = $this->argument('reservationId');

        $organization = $this->organizations->query()->findOrFail($organizationId);

        try {
            $this->klarnaService->cancelReservation($organization->payment_id, $organization->payment_secret, $reservationId);
        } catch (\KlarnaException $exception) {
            $this->klarnaService->cancelReservation(config('klarna.kkj_payment_id'), config('klarna.kkj_payment_secret'), $reservationId);
        }
    }
}
