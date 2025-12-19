<?php

namespace App\Repositories;

use App\Models\MaterielRequest;
use App\Models\MrBulkUploadErrorLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class MrBulkUploadErrorLogRepository
 * @package App\Repositories
 * @version September 8, 2024, 2:17 pm +04
 *
 * @method MrBulkUploadErrorLog findWithoutFail($id, $columns = ['*'])
 * @method MrBulkUploadErrorLog find($id, $columns = ['*'])
 * @method MrBulkUploadErrorLog first($columns = ['*'])
*/
class MrBulkUploadErrorLogRepository extends BaseRepository
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
        return MrBulkUploadErrorLog::class;
    }

    public function getBulkUploadErrors($poId)
    {
        $errorCount = MrBulkUploadErrorLog::where('documentSystemID', trim($poId))->count();

        $details = MrBulkUploadErrorLog::where('documentSystemID', trim($poId))
            ->take(10)
            ->get();

        $successCount = MaterielRequest::where('RequestID', trim($poId))
            ->pluck('successDetailsCount')
            ->first();

        $excelRowCount = MaterielRequest::where('RequestID', trim($poId))
            ->pluck('excelRowCount')
            ->first();

        $error = [
            'errorRecordCount' => $errorCount,
            'successRecordCount' => $successCount,
            'errorDetails' => $details,
            'totalRecords' => $excelRowCount
        ];

        return $error;
    }
}
