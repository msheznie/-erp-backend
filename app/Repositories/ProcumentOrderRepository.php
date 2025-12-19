<?php

namespace App\Repositories;

use App\Models\ProcumentOrder;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ProcumentOrderRepository
 * @package App\Repositories
 * @version March 28, 2018, 7:42 am UTC
 *
 * @method ProcumentOrder findWithoutFail($id, $columns = ['*'])
 * @method ProcumentOrder find($id, $columns = ['*'])
 * @method ProcumentOrder first($columns = ['*'])
*/
class ProcumentOrderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'poProcessId',
        'companyID',
        'departmentID',
        'serviceLine',
        'companyAddress',
        'documentID',
        'purchaseOrderCode',
        'serialNumber',
        'supplierID',
        'supplierPrimaryCode',
        'supplierName',
        'supplierAddress',
        'supplierTelephone',
        'supplierFax',
        'supplierEmail',
        'creditPeriod',
        'expectedDeliveryDate',
        'narration',
        'poLocation',
        'financeCategory',
        'referenceNumber',
        'shippingAddressID',
        'shippingAddressDescriprion',
        'invoiceToAddressID',
        'invoiceToAddressDescription',
        'soldToAddressID',
        'soldToAddressDescriprion',
        'paymentTerms',
        'deliveryTerms',
        'panaltyTerms',
        'localCurrencyID',
        'localCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierTransactionCurrencyID',
        'supplierTransactionER',
        'poConfirmedYN',
        'poConfirmedByEmpID',
        'poConfirmedByName',
        'poConfirmedDate',
        'poCancelledYN',
        'poCancelledBy',
        'poCancelledByName',
        'poCancelledDate',
        'cancelledComments',
        'poTotalComRptCurrency',
        'poTotalLocalCurrency',
        'poTotalSupplierDefaultCurrency',
        'poTotalSupplierTransactionCurrency',
        'poDiscountPercentage',
        'poDiscountAmount',
        'supplierVATEligible',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'shipTocontactPersonID',
        'shipTocontactPersonTelephone',
        'shipTocontactPersonFaxNo',
        'shipTocontactPersonEmail',
        'invoiceTocontactPersonID',
        'invoiceTocontactPersonTelephone',
        'invoiceTocontactPersonFaxNo',
        'invoiceTocontactPersonEmail',
        'soldTocontactPersonID',
        'soldTocontactPersonTelephone',
        'soldTocontactPersonFaxNo',
        'soldTocontactPersonEmail',
        'priority',
        'approved',
        'approvedDate',
        'addOnPercent',
        'addOnDefaultPercent',
        'GRVTrackingID',
        'logisticDoneYN',
        'poClosedYN',
        'grvRecieved',
        'invoicedBooked',
        'timesReferred',
        'poType',
        'poType_N',
        'docRefNo',
        'RollLevForApp_curr',
        'sentToSupplier',
        'sentToSupplierByEmpID',
        'sentToSupplierByEmpName',
        'sentToSupplierDate',
        'budgetBlockYN',
        'budgetYear',
        'hidePOYN',
        'hideByEmpID',
        'hideByEmpName',
        'hideDate',
        'hideComments',
        'WO_purchaseOrderID',
        'WO_PeriodFrom',
        'WO_PeriodTo',
        'WO_NoOfAutoGenerationTimes',
        'WO_NoOfGeneratedTimes',
        'WO_fullyGenerated',
        'WO_amendYN',
        'WO_amendRequestedDate',
        'WO_amendRequestedByEmpID',
        'WO_confirmedYN',
        'WO_confirmedDate',
        'WO_confirmedByEmpID',
        'WO_terminateYN',
        'WO_terminatedDate',
        'WO_terminatedByEmpID',
        'WO_terminateComments',
        'partiallyGRVAllowed',
        'logisticsAvailable',
        'vatRegisteredYN',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'isSelected',
        'timeStamp',
        'upload_job_status'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ProcumentOrder::class;
    }

    public function swapValue ($value){

        if($value == -1){
             return [
                'value'=>0,
                'text'=>trans('custom.deactivated')
                ];

        }elseif ($value == 0) {
            return [
                'value'=>-1,
                'text'=>trans('custom.activated')
            ];
        }

    }
}
