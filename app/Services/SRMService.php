<?php

namespace App\Services;

use App\Models\PrintTemplate;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SRMService
{
    public function __construct() {}

    /**
     * get currencies
     * @return array
     */
    public function getCurrencies(): array {
        $data = [
            'LKR',
            'USD',
            'ASD'
        ];

        return [
            'success'   => true,
            'message'   => 'currencies successfully get',
            'data'      => $data
        ];
    }


}