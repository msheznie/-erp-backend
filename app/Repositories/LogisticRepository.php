<?php

namespace App\Repositories;

use App\Models\Logistic;
use App\Repositories\BaseRepository;

/**
 * Class LogisticRepository
 * @package App\Repositories
 * @version September 12, 2018, 5:06 am UTC
 *
 * @method Logistic findWithoutFail($id, $columns = ['*'])
 * @method Logistic find($id, $columns = ['*'])
 * @method Logistic first($columns = ['*'])
*/
class LogisticRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'logisticDocCode',
        'comments',
        'supplierID',
        'logisticShippingModeID',
        'modeOfImportID',
        'nextCustomDocRenewalDate',
        'customDocRenewalHistory',
        'customInvoiceNo',
        'customInvoiceDate',
        'customInvoiceCurrencyID',
        'customInvoiceAmount',
        'customInvoiceLocalCurrencyID',
        'customInvoiceLocalER',
        'customInvoiceLocalAmount',
        'customInvoiceRptCurrencyID',
        'customInvoiceRptER',
        'customInvoiceRptAmount',
        'airwayBillNo',
        'totalWeight',
        'totalWeightUOM',
        'totalVolume',
        'totalVolumeUOM',
        'customeArrivalDate',
        'deliveryDate',
        'billofEntryDate',
        'billofEntryNo',
        'agentDeliveryLocationID',
        'agentDOnumber',
        'agentDOdate',
        'agentID',
        'agentFeeCurrencyID',
        'agentFee',
        'agentFeeLocalAmount',
        'agenFeeRptAmount',
        'customDutyFeeCurrencyID',
        'customDutyFeeAmount',
        'customDutyFeeLocalAmount',
        'customDutyFeeRptAmount',
        'customDutyTotalAmount',
        'shippingOriginPort',
        'shippingOriginCountry',
        'shippingOriginDate',
        'shippingDestinationPort',
        'shippingDestinationCountry',
        'shippingDestinationDate',
        'ftaOrDF',
        'createdUserID',
        'createdUserSystemID',
        'createdPCid',
        'createdDateTime',
        'modifiedUserID',
        'modifiedUserSystemID',
        'modifiedPCID',
        'modifiedDate',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Logistic::class;
    }

    public function getAudit($id)
    {
        return $this->with(['created_by', 'modified_by', 'company', 'details' => function ($q) {
            //$q->with('uom_issuing', 'item_by');
        }])->findWithoutFail($id);
    }
}
