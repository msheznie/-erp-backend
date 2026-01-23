<?php

namespace App\Repositories;

use App\Models\DocumentReferedHistory;
use App\Repositories\BaseRepository;

/**
 * Class DocumentReferedHistoryRepository
 * @package App\Repositories
 * @version July 23, 2018, 1:08 pm UTC
 *
 * @method DocumentReferedHistory findWithoutFail($id, $columns = ['*'])
 * @method DocumentReferedHistory find($id, $columns = ['*'])
 * @method DocumentReferedHistory first($columns = ['*'])
*/
class DocumentReferedHistoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'departmentSystemID',
        'departmentID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'documentDate',
        'approvalLevelID',
        'rollID',
        'approvalGroupID',
        'rollLevelOrder',
        'employeeSystemID',
        'employeeID',
        'docConfirmedDate',
        'docConfirmedByEmpSystemID',
        'docConfirmedByEmpID',
        'preRollApprovedDate',
        'approvedYN',
        'approvedDate',
        'approvedComments',
        'rejectedYN',
        'rejectedDate',
        'rejectedComments',
        'approvedPCID',
        'timeStamp',
        'refTimes'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentReferedHistory::class;
    }
}
