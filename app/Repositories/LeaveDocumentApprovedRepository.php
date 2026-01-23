<?php

namespace App\Repositories;

use App\Models\LeaveDocumentApproved;
use App\Repositories\BaseRepository;

/**
 * Class LeaveDocumentApprovedRepository
 * @package App\Repositories
 * @version September 3, 2019, 10:57 am +04
 *
 * @method LeaveDocumentApproved findWithoutFail($id, $columns = ['*'])
 * @method LeaveDocumentApproved find($id, $columns = ['*'])
 * @method LeaveDocumentApproved first($columns = ['*'])
*/
class LeaveDocumentApprovedRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyID',
        'departmentID',
        'serviceLineCode',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'approvalLevelID',
        'rollID',
        'rollLevelOrder',
        'employeeID',
        'Approver',
        'docConfirmedDate',
        'docConfirmedByEmpID',
        'preRollApprovedDate',
        'requesterID',
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
        'timeStamp',
        'approvalGroupID',
        'hrApproval'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LeaveDocumentApproved::class;
    }
}
