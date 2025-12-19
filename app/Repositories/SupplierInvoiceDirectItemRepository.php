<?php

namespace App\Repositories;

use App\Models\SupplierInvoiceDirectItem;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierInvoiceDirectItemRepository
 * @package App\Repositories
 * @version February 22, 2022, 10:33 am +04
 *
 * @method SupplierInvoiceDirectItem findWithoutFail($id, $columns = ['*'])
 * @method SupplierInvoiceDirectItem find($id, $columns = ['*'])
 * @method SupplierInvoiceDirectItem first($columns = ['*'])
*/
class SupplierInvoiceDirectItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
       'bookingSuppMasInvAutoID',
        'companySystemID',
        'itemCode',
        'itemPrimaryCode',
        'itemDescription',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodePLSystemID',
        'includePLForGRVYN',
        'supplierPartNumber',
        'unitOfMeasure',
        'trackingType',
        'noQty',
        'unitCost',
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
        'costPerUnitLocalCur',
        'costPerUnitSupDefaultCur',
        'costPerUnitSupTransCur',
        'costPerUnitComRptCur',
        'discountPercentage',
        'discountAmount',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'VATApplicableOn',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'exempt_vat_portion',
        'timesReferred',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierInvoiceDirectItem::class;
    }
}
