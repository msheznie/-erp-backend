<?php

namespace App\Repositories;

use App\Models\POSSTAGTaxMaster;
use App\Repositories\BaseRepository;

/**
 * Class POSSTAGTaxMasterRepository
 * @package App\Repositories
 * @version August 8, 2022, 2:56 pm +04
 *
 * @method POSSTAGTaxMaster findWithoutFail($id, $columns = ['*'])
 * @method POSSTAGTaxMaster find($id, $columns = ['*'])
 * @method POSSTAGTaxMaster first($columns = ['*'])
*/
class POSSTAGTaxMasterRepository extends BaseRepository
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
        return POSSTAGTaxMaster::class;
    }
}
