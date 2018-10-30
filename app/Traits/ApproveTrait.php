<?php

namespace App\Traits;
use App\Scopes\ApproveScope;

trait ApproveTrait
{
    /**
     * Boot the Active Events trait for a model.
     *
     * @return void
     */
    public static function bootApproveTrait()
    {
        static::addGlobalScope(new ApproveScope());
    }
}