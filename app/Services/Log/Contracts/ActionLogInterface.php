<?php

namespace Jakten\Services\Log\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ActionLogInterface
{
    /**
     * @param Model $model
     * @param string|null $action
     * @return bool
     */
    public function logAction(Model $model, string $action = null): bool;
    
    /**
     * Add log about creating a model
     *
     * @param Model $model
     * @return bool
     */
    public function logCreate(Model $model): bool;
    
    /**
     * Add log about deleting a model
     *
     * @param Model $model
     * @return bool
     */
    public function logDelete(Model $model): bool;
}