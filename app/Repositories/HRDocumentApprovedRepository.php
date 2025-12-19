<?php

namespace App\Repositories;

use App\Models\HRDocumentApproved;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class HRDocumentApprovedRepository
 * @package App\Repositories
 * @version April 3, 2023, 9:18 am +04
 *
 * @method HRDocumentApproved findWithoutFail($id, $columns = ['*'])
 * @method HRDocumentApproved find($id, $columns = ['*'])
 * @method HRDocumentApproved first($columns = ['*'])
*/
class HRDocumentApprovedRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'wareHouseAutoID',
        'departmentID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'isCancel',
        'documentDate',
        'approvalLevelID',
        'isReverseApplicableYN',
        'roleID',
        'leaveSetupID',
        'approvalGroupID',
        'roleLevelOrder',
        'docConfirmedDate',
        'docConfirmedByEmpID',
        'table_name',
        'table_unique_field_name',
        'approvedEmpID',
        'approvedYN',
        'approvedDate',
        'approvedComments',
        'approvedPC',
        'companyID',
        'companyCode',
        'timeStamp',
        'is_sync',
        'id_store'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HRDocumentApproved::class;
    }
}
