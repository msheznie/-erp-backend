<?php

namespace App\Repositories;

use App\Models\DocumentApproved;
use App\Repositories\BaseRepository;

/**
 * Class DocumentApprovedRepository
 * @package App\Repositories
 * @version March 29, 2018, 6:31 am UTC
 *
 * @method DocumentApproved findWithoutFail($id, $columns = ['*'])
 * @method DocumentApproved find($id, $columns = ['*'])
 * @method DocumentApproved first($columns = ['*'])
*/
class DocumentApprovedRepository extends BaseRepository
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
        'employeeID',
        'docConfirmedDate',
        'docConfirmedByEmpID',
        'preRollApprovedDate',
        'approvedYN',
        'approvedDate',
        'approvedComments',
        'rejectedYN',
        'rejectedDate',
        'rejectedComments',
        'myApproveFlag',
        'isDeligationApproval',
        'approvedForEmpID',
        'isApprovedFromPC',
        'approvedPCID',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentApproved::class;
    }
}
