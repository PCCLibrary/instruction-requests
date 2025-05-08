<?php

namespace App\Exceptions;

use TestMonitor\Lockable\Exceptions\ModelLockedException as BaseModelLockedException;

class ModelLockedException extends BaseModelLockedException
{
    /**
     * Set the affected Eloquent model.
     *
     * Override the parent method to provide a more user-friendly error message
     * without exposing the entire model data structure.
     *
     * @param \TestMonitor\Lockable\Contracts\IsLockable $model
     * @return static
     */
    public function setModel($model): static
    {
        $this->model = $model;

        // Get the user who locked the record (if available)
        $locker = $model->lockedBy ?? null;
        $lockerName = $locker ? $locker->display_name : 'Another user';

        // Set a clean, user-friendly message
        $this->message = "{$lockerName} is currently editing this request.";

        return $this;
    }
}
