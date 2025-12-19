<?php

namespace App\Repositories;

use App\Models\GrvDetailsRefferedback;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class GrvDetailsRefferedbackRepository
 * @package App\Repositories
 * @version December 14, 2018, 6:21 am UTC
 *
 * @method GrvDetailsRefferedback findWithoutFail($id, $columns = ['*'])
 * @method GrvDetailsRefferedback find($id, $columns = ['*'])
 * @method GrvDetailsRefferedback first($columns = ['*'])
*/
class GrvDetailsRefferedbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'grvDetailsID',
        'grvAutoID',
        'companySystemID',
        'companyID',
        'serviceLineCode',
        'purchaseOrderMastertID',
        'purchaseOrderDetailsID',
        'itemCode',
        'itemPrimaryCode',
        'itemDescription',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'supplierPartNumber',
        'unitOfMeasure',
        'noQty',
        'prvRecievedQty',
        'poQty',
        'unitCost',
        'discountPercentage',
        'discountAmount',
        'netAmount',
        'comment',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierItemCurrencyID',
        'foreignToLocalER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'addonDistCost',
        'GRVcostPerUnitLocalCur',
        'GRVcostPerUnitSupDefaultCur',
        'GRVcostPerUnitSupTransCur',
        'GRVcostPerUnitComRptCur',
        'landingCost_TransCur',
        'landingCost_LocalCur',
        'landingCost_RptCur',
        'logisticsCharges_TransCur',
        'logisticsCharges_LocalCur',
        'logisticsChargest_RptCur',
        'assetAllocationDoneYN',
        'isContract',
        'timesReferred',
        'totalWHTAmount',
        'WHTBearedBySupplier',
        'WHTBearedByCompany',
        'extraComment',
        'vatRegisteredYN',
        'supplierVATEligible',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'logisticsAvailable',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return GrvDetailsRefferedback::class;
    }
}
