<?php

namespace App\Repositories;

use App\Models\ItemIssueMaster;
use App\Models\MiBulkUploadErrorLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class MiBulkUploadErrorLogRepository
 * @package App\Repositories
 * @version September 9, 2024, 10:59 am +04
 *
 * @method MiBulkUploadErrorLog findWithoutFail($id, $columns = ['*'])
 * @method MiBulkUploadErrorLog find($id, $columns = ['*'])
 * @method MiBulkUploadErrorLog first($columns = ['*'])
*/
class MiBulkUploadErrorLogRepository extends BaseRepository
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
        return MiBulkUploadErrorLog::class;
    }

    public function getBulkUploadErrors($miId)
    {
        $errorCount = MiBulkUploadErrorLog::where('documentSystemID', trim($miId))->count();

        $details = MiBulkUploadErrorLog::where('documentSystemID', trim($miId))
            ->take(10)
            ->get();

        $successCount = ItemIssueMaster::where('itemIssueAutoID', trim($miId))
            ->pluck('successDetailsCount')
            ->first();

        $excelRowCount = ItemIssueMaster::where('itemIssueAutoID', trim($miId))
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
