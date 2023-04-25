<?php namespace Jakten\Repositories\Contracts;

/**
 * Interface GiftCardTypeRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface GiftCardTypeRepositoryContract extends BaseRepositoryContract
{
    /**
     * @return mixed
     */
    public function getAll();

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * @param array $ids
     * @return mixed
     */
    public function getByIds(array $ids);

}
