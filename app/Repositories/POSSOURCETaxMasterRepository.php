<?php

namespace App\Repositories;

use App\Models\POSSOURCETaxMaster;
use App\Repositories\BaseRepository;

/**
 * Class POSSOURCETaxMasterRepository
 * @package App\Repositories
 * @version August 8, 2022, 2:57 pm +04
 *
 * @method POSSOURCETaxMaster findWithoutFail($id, $columns = ['*'])
 * @method POSSOURCETaxMaster find($id, $columns = ['*'])
 * @method POSSOURCETaxMaster first($columns = ['*'])
*/
class POSSOURCETaxMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyCode',
        'companyID',
        'createdDateTime',
        'createdPCID',
        'createdUserGroup',
        'createdUserID',
        'createdUserName',
        'effectiveFrom',
        'erp_tax_master_id',
        'inputVatGLAccountAutoID',
        'inputVatTransferGLAccountAutoID',
        'isActive',
        'isClaimable',
        'modifiedDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'outputVatGLAccountAutoID',
        'outputVatTransferGLAccountAutoID',
        'supplierGLAutoID',
        'taxCategory',
        'taxDescription',
        'taxPercentage',
        'taxReferenceNo',
        'taxShortCode',
        'taxType',
        'timestamp',
        'transaction_log_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSSOURCETaxMaster::class;
    }
}
