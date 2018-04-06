<?php

namespace App\Repositories;

use App\Models\GRVDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class GRVDetailsRepository
 * @package App\Repositories
 * @version April 2, 2018, 3:53 am UTC
 *
 * @method GRVDetails findWithoutFail($id, $columns = ['*'])
 * @method GRVDetails find($id, $columns = ['*'])
 * @method GRVDetails first($columns = ['*'])
*/
class GRVDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'grvAutoID',
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
        return GRVDetails::class;
    }
}
