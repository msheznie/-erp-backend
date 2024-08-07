<?php

namespace App\Repositories;

use App\Models\PoBulkUploadErrorLog;
use App\Models\ProcumentOrder;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PoBulkUploadErrorLogRepository
 * @package App\Repositories
 * @version August 5, 2024, 3:40 pm +0530
 *
 * @method PoBulkUploadErrorLog findWithoutFail($id, $columns = ['*'])
 * @method PoBulkUploadErrorLog find($id, $columns = ['*'])
 * @method PoBulkUploadErrorLog first($columns = ['*'])
*/
class PoBulkUploadErrorLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'error'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PoBulkUploadErrorLog::class;
    }

    public function getBulkUploadErrors($poId)
    {
        $errorCount = PoBulkUploadErrorLog::where('documentSystemID', trim($poId))->count();

        $details = PoBulkUploadErrorLog::where('documentSystemID', trim($poId))
            ->take(10)
            ->get();

        $successCount = ProcumentOrder::where('purchaseOrderID', trim($poId))
            ->pluck('successDetailsCount')
            ->first();

        $error = [
            'errorRecordCount' => $errorCount,
            'successRecordCount' => $successCount,
            'errorDetails' => $details,
            'totalRecords' => ($errorCount + $successCount)
        ];

        return $error;
    }
}
