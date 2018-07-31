<?php

namespace App\Repositories;

use App\Models\PurchaseReturnDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PurchaseReturnDetailsRepository
 * @package App\Repositories
 * @version July 31, 2018, 6:20 am UTC
 *
 * @method PurchaseReturnDetails findWithoutFail($id, $columns = ['*'])
 * @method PurchaseReturnDetails find($id, $columns = ['*'])
 * @method PurchaseReturnDetails first($columns = ['*'])
*/
class PurchaseReturnDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'purhaseReturnAutoID',
        'companyID',
        'grvAutoID',
        'grvDetailsID',
        'itemCode',
        'itemPrimaryCode',
        'itemDescription',
        'supplierPartNumber',
        'unitOfMeasure',
        'GRVQty',
        'comment',
        'noQty',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierTransactionCurrencyID',
        'supplierTransactionER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'GRVcostPerUnitLocalCur',
        'GRVcostPerUnitSupDefaultCur',
        'GRVcostPerUnitSupTransCur',
        'GRVcostPerUnitComRptCur',
        'netAmount',
        'netAmountLocal',
        'netAmountRpt',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseReturnDetails::class;
    }
}
