<?php

namespace App\Jobs\Concerns;

trait UsesDepreciationQueueConnection
{
    protected function configureQueueConnection(): void
    {
        if (env('QUEUE_DRIVER_CHANGE', 'database') == 'database') {
            if (env('IS_MULTI_TENANCY', false)) {
                self::onConnection('database_main');
            } else {
                self::onConnection('database');
            }

            return;
        }

        self::onConnection(env('QUEUE_DRIVER_CHANGE', 'database'));
    }
}
