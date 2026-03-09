<?php

namespace App\Repositories;

use App\Models\PurchaseReturnDetailsRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class PurchaseReturnDetailsRefferedBackRepository
 * @package App\Repositories
 * @version January 25, 2021, 12:47 pm +04
 *
 * @method PurchaseReturnDetailsRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method PurchaseReturnDetailsRefferedBack find($id, $columns = ['*'])
 * @method PurchaseReturnDetailsRefferedBack first($columns = ['*'])
*/
class PurchaseReturnDetailsRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'purhasereturnDetailID',
        'purhaseReturnAutoID',
        'companyID',
        'grvAutoID',
        'grvDetailsID',
        'itemCode',
        'itemPrimaryCode',
        'itemDescription',
        'supplierPartNumber',
        'unitOfMeasure',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
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
        'timeStamp',
        'GRVSelectedYN',
        'goodsRecievedYN',
        'receivedQty'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseReturnDetailsRefferedBack::class;
    }
}
