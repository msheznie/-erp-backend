<?php

use Faker\Factory as Faker;
use App\Models\ProcumentOrder;
use App\Repositories\ProcumentOrderRepository;

trait MakeProcumentOrderTrait
{
    /**
     * Create fake instance of ProcumentOrder and save it in database
     *
     * @param array $procumentOrderFields
     * @return ProcumentOrder
     */
    public function makeProcumentOrder($procumentOrderFields = [])
    {
        /** @var ProcumentOrderRepository $procumentOrderRepo */
        $procumentOrderRepo = App::make(ProcumentOrderRepository::class);
        $theme = $this->fakeProcumentOrderData($procumentOrderFields);
        return $procumentOrderRepo->create($theme);
    }

    /**
     * Get fake instance of ProcumentOrder
     *
     * @param array $procumentOrderFields
     * @return ProcumentOrder
     */
    public function fakeProcumentOrder($procumentOrderFields = [])
    {
        return new ProcumentOrder($this->fakeProcumentOrderData($procumentOrderFields));
    }

    /**
     * Get fake data of ProcumentOrder
     *
     * @param array $postFields
     * @return array
     */
    public function fakeProcumentOrderData($procumentOrderFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'poProcessId' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'departmentID' => $fake->word,
            'serviceLine' => $fake->word,
            'companyAddress' => $fake->text,
            'documentID' => $fake->word,
            'purchaseOrderCode' => $fake->word,
            'serialNumber' => $fake->randomDigitNotNull,
            'supplierID' => $fake->randomDigitNotNull,
            'supplierPrimaryCode' => $fake->word,
            'supplierName' => $fake->text,
            'supplierAddress' => $fake->text,
            'supplierTelephone' => $fake->word,
            'supplierFax' => $fake->word,
            'supplierEmail' => $fake->word,
            'creditPeriod' => $fake->randomDigitNotNull,
            'expectedDeliveryDate' => $fake->date('Y-m-d H:i:s'),
            'narration' => $fake->text,
            'poLocation' => $fake->randomDigitNotNull,
            'financeCategory' => $fake->randomDigitNotNull,
            'referenceNumber' => $fake->word,
            'shippingAddressID' => $fake->randomDigitNotNull,
            'shippingAddressDescriprion' => $fake->text,
            'invoiceToAddressID' => $fake->randomDigitNotNull,
            'invoiceToAddressDescription' => $fake->text,
            'soldToAddressID' => $fake->randomDigitNotNull,
            'soldToAddressDescriprion' => $fake->text,
            'paymentTerms' => $fake->text,
            'deliveryTerms' => $fake->text,
            'panaltyTerms' => $fake->text,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingER' => $fake->randomDigitNotNull,
            'supplierDefaultCurrencyID' => $fake->randomDigitNotNull,
            'supplierDefaultER' => $fake->randomDigitNotNull,
            'supplierTransactionCurrencyID' => $fake->randomDigitNotNull,
            'supplierTransactionER' => $fake->randomDigitNotNull,
            'poConfirmedYN' => $fake->randomDigitNotNull,
            'poConfirmedByEmpID' => $fake->word,
            'poConfirmedByName' => $fake->word,
            'poConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'poCancelledYN' => $fake->randomDigitNotNull,
            'poCancelledBy' => $fake->word,
            'poCancelledByName' => $fake->word,
            'poCancelledDate' => $fake->date('Y-m-d H:i:s'),
            'cancelledComments' => $fake->word,
            'poTotalComRptCurrency' => $fake->randomDigitNotNull,
            'poTotalLocalCurrency' => $fake->randomDigitNotNull,
            'poTotalSupplierDefaultCurrency' => $fake->randomDigitNotNull,
            'poTotalSupplierTransactionCurrency' => $fake->randomDigitNotNull,
            'poDiscountPercentage' => $fake->randomDigitNotNull,
            'poDiscountAmount' => $fake->randomDigitNotNull,
            'supplierVATEligible' => $fake->randomDigitNotNull,
            'VATPercentage' => $fake->randomDigitNotNull,
            'VATAmount' => $fake->randomDigitNotNull,
            'VATAmountLocal' => $fake->randomDigitNotNull,
            'VATAmountRpt' => $fake->randomDigitNotNull,
            'shipTocontactPersonID' => $fake->word,
            'shipTocontactPersonTelephone' => $fake->word,
            'shipTocontactPersonFaxNo' => $fake->word,
            'shipTocontactPersonEmail' => $fake->word,
            'invoiceTocontactPersonID' => $fake->word,
            'invoiceTocontactPersonTelephone' => $fake->word,
            'invoiceTocontactPersonFaxNo' => $fake->word,
            'invoiceTocontactPersonEmail' => $fake->word,
            'soldTocontactPersonID' => $fake->word,
            'soldTocontactPersonTelephone' => $fake->word,
            'soldTocontactPersonFaxNo' => $fake->word,
            'soldTocontactPersonEmail' => $fake->word,
            'priority' => $fake->randomDigitNotNull,
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'addOnPercent' => $fake->randomDigitNotNull,
            'addOnDefaultPercent' => $fake->randomDigitNotNull,
            'GRVTrackingID' => $fake->randomDigitNotNull,
            'logisticDoneYN' => $fake->randomDigitNotNull,
            'poClosedYN' => $fake->randomDigitNotNull,
            'grvRecieved' => $fake->randomDigitNotNull,
            'invoicedBooked' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'poType' => $fake->word,
            'poType_N' => $fake->randomDigitNotNull,
            'docRefNo' => $fake->word,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'sentToSupplier' => $fake->randomDigitNotNull,
            'sentToSupplierByEmpID' => $fake->word,
            'sentToSupplierByEmpName' => $fake->word,
            'sentToSupplierDate' => $fake->date('Y-m-d H:i:s'),
            'budgetBlockYN' => $fake->randomDigitNotNull,
            'budgetYear' => $fake->randomDigitNotNull,
            'hidePOYN' => $fake->randomDigitNotNull,
            'hideByEmpID' => $fake->word,
            'hideByEmpName' => $fake->word,
            'hideDate' => $fake->date('Y-m-d H:i:s'),
            'hideComments' => $fake->text,
            'WO_purchaseOrderID' => $fake->randomDigitNotNull,
            'WO_PeriodFrom' => $fake->date('Y-m-d H:i:s'),
            'WO_PeriodTo' => $fake->date('Y-m-d H:i:s'),
            'WO_NoOfAutoGenerationTimes' => $fake->randomDigitNotNull,
            'WO_NoOfGeneratedTimes' => $fake->randomDigitNotNull,
            'WO_fullyGenerated' => $fake->randomDigitNotNull,
            'WO_amendYN' => $fake->randomDigitNotNull,
            'WO_amendRequestedDate' => $fake->date('Y-m-d H:i:s'),
            'WO_amendRequestedByEmpID' => $fake->word,
            'WO_confirmedYN' => $fake->randomDigitNotNull,
            'WO_confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'WO_confirmedByEmpID' => $fake->word,
            'WO_terminateYN' => $fake->randomDigitNotNull,
            'WO_terminatedDate' => $fake->date('Y-m-d H:i:s'),
            'WO_terminatedByEmpID' => $fake->word,
            'WO_terminateComments' => $fake->text,
            'partiallyGRVAllowed' => $fake->randomDigitNotNull,
            'logisticsAvailable' => $fake->randomDigitNotNull,
            'vatRegisteredYN' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'isSelected' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $procumentOrderFields);
    }
}
