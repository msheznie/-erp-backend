<?php

namespace App\Repositories;

use App\Models\AssetTransferReferredback;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AssetTransferReferredbackRepository
 * @package App\Repositories
 * @version July 29, 2021, 4:30 pm +04
 *
 * @method AssetTransferReferredback findWithoutFail($id, $columns = ['*'])
 * @method AssetTransferReferredback find($id, $columns = ['*'])
 * @method AssetTransferReferredback first($columns = ['*'])
*/
class AssetTransferReferredbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'serviceLineSystemID',
        'purchaseRequestCode',
        'budgetYear',
        'prBelongsYear',
        'document_id',
        'document_code',
        'type',
        'location',
        'reference_no',
        'document_date',
        'approval_comments',
        'serial_no',
        'emp_id',
        'narration',
        'refferedBackYN',
        'serviceLineCode',
        'company_id',
        'company_code',
        'confirmed_yn',
        'confirmed_by_emp_id',
        'confirmedByName',
        'confirmedByEmpID',
        'confirmed_date',
        'documentSystemID',
        'approved_yn',
        'approved_date',
        'approved_by_emp_name',
        'approved_by_emp_id',
        'current_level_no',
        'timesReferred',
        'created_user_id',
        'purchaseRequestID',
        'updated_user_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AssetTransferReferredback::class;
    }
}
