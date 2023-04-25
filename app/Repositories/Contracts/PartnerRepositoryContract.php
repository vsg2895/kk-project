<?php

namespace Jakten\Repositories\Contracts;

use Illuminate\Http\Request;
use Jakten\Models\Partner;
use Jakten\Repositories\PartnerRepository;

/**
 * Interface PartnerRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface PartnerRepositoryContract extends BaseRepositoryContract
{

    /**
     * @return PartnerRepository
     */
    public function isActive();

    /**
     * @param Request $request
     * @return PartnerRepository
     */
    public function paginate(Request $request);

    /**
     * @param array $data
     * @return Partner|null|false
     * @throws \Exception
     */
    public function create(array $data = []);

    /**
     * @param string $order
     * @param string $field
     * @return PartnerRepository
     */
    public function order(string $order = 'desc', string $field = 'id');

}
