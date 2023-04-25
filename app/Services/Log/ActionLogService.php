<?php

namespace Jakten\Services\Log;

use Illuminate\Database\Eloquent\Model;
use Jakten\Facades\Auth;
use Jakten\Models\Log;
use Jakten\Services\Log\Contracts\ActionLogInterface;

/**
 * Class LogService
 * @package Jakten\Services\Log
 */
class ActionLogService implements ActionLogInterface
{
    /**
     * @inheritdoc
     */
    public function logAction(Model $model, string $comment = null): bool
    {
        $log = new Log();
        $log->comment = $comment;
        $log->loggable()->associate($model);
        $log->user()->associate(Auth::user());
        return $log->save();
    }
    
    /**
     * @inheritdoc
     */
    public function logCreate(Model $model): bool
    {
        return $this->logAction($model, 'create');
    }
    
    /**
     * @inheritdoc
     */
    public function logDelete(Model $model): bool
    {
        return $this->logAction($model, 'delete');
    }
}