<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class POSService
{


    public function __construct()
    {
    }

    public function posInvoice(Request $request)
    {
        $param = '';
        $mappingData = self::getMappingData($param);

        if ($mappingData['success'] == false) {
            return [
                'success' => true,
                'message' => 'Pos',
                'data' => $mappingData['message']
            ];
        }

        return [
            'success' => true,
            'message' => 'Pos',
            'data' => $mappingData['message']
        ];
    }

    public function getMappingData($param)
    {
        if (!Schema::hasTable('pos_mapping_master') || !Schema::hasTable('pos_mapping_detail')) {
            return [
                'success' => false,
                'message' => 'Mapping table does not exist',
                'data' => null
            ];
        } else {
            return [
                'success' => true,
                'message' => 'TTTTTasdasdasd',
                'data' => ' '
            ];
        }
    }
}
