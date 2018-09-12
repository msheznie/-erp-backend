<?php

use Faker\Factory as Faker;
use App\Models\Logistic;
use App\Repositories\LogisticRepository;

trait MakeLogisticTrait
{
    /**
     * Create fake instance of Logistic and save it in database
     *
     * @param array $logisticFields
     * @return Logistic
     */
    public function makeLogistic($logisticFields = [])
    {
        /** @var LogisticRepository $logisticRepo */
        $logisticRepo = App::make(LogisticRepository::class);
        $theme = $this->fakeLogisticData($logisticFields);
        return $logisticRepo->create($theme);
    }

    /**
     * Get fake instance of Logistic
     *
     * @param array $logisticFields
     * @return Logistic
     */
    public function fakeLogistic($logisticFields = [])
    {
        return new Logistic($this->fakeLogisticData($logisticFields));
    }

    /**
     * Get fake data of Logistic
     *
     * @param array $postFields
     * @return array
     */
    public function fakeLogisticData($logisticFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'logisticDocCode' => $fake->word,
            'comments' => $fake->text,
            'supplierID' => $fake->randomDigitNotNull,
            'logisticShippingModeID' => $fake->randomDigitNotNull,
            'modeOfImportID' => $fake->randomDigitNotNull,
            'nextCustomDocRenewalDate' => $fake->date('Y-m-d H:i:s'),
            'customDocRenewalHistory' => $fake->text,
            'customInvoiceNo' => $fake->word,
            'customInvoiceDate' => $fake->date('Y-m-d H:i:s'),
            'customInvoiceCurrencyID' => $fake->randomDigitNotNull,
            'customInvoiceAmount' => $fake->randomDigitNotNull,
            'customInvoiceLocalCurrencyID' => $fake->randomDigitNotNull,
            'customInvoiceLocalER' => $fake->randomDigitNotNull,
            'customInvoiceLocalAmount' => $fake->randomDigitNotNull,
            'customInvoiceRptCurrencyID' => $fake->randomDigitNotNull,
            'customInvoiceRptER' => $fake->randomDigitNotNull,
            'customInvoiceRptAmount' => $fake->randomDigitNotNull,
            'airwayBillNo' => $fake->word,
            'totalWeight' => $fake->randomDigitNotNull,
            'totalWeightUOM' => $fake->randomDigitNotNull,
            'totalVolume' => $fake->randomDigitNotNull,
            'totalVolumeUOM' => $fake->randomDigitNotNull,
            'customeArrivalDate' => $fake->date('Y-m-d H:i:s'),
            'deliveryDate' => $fake->date('Y-m-d H:i:s'),
            'billofEntryDate' => $fake->date('Y-m-d H:i:s'),
            'billofEntryNo' => $fake->word,
            'agentDeliveryLocationID' => $fake->randomDigitNotNull,
            'agentDOnumber' => $fake->word,
            'agentDOdate' => $fake->date('Y-m-d H:i:s'),
            'agentID' => $fake->randomDigitNotNull,
            'agentFeeCurrencyID' => $fake->randomDigitNotNull,
            'agentFee' => $fake->randomDigitNotNull,
            'agentFeeLocalAmount' => $fake->randomDigitNotNull,
            'agenFeeRptAmount' => $fake->randomDigitNotNull,
            'customDutyFeeCurrencyID' => $fake->randomDigitNotNull,
            'customDutyFeeAmount' => $fake->randomDigitNotNull,
            'customDutyFeeLocalAmount' => $fake->randomDigitNotNull,
            'customDutyFeeRptAmount' => $fake->randomDigitNotNull,
            'customDutyTotalAmount' => $fake->randomDigitNotNull,
            'shippingOriginPort' => $fake->word,
            'shippingOriginCountry' => $fake->word,
            'shippingOriginDate' => $fake->date('Y-m-d H:i:s'),
            'shippingDestinationPort' => $fake->word,
            'shippingDestinationCountry' => $fake->word,
            'shippingDestinationDate' => $fake->date('Y-m-d H:i:s'),
            'ftaOrDF' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdPCid' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedUserID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedPCID' => $fake->word,
            'modifiedDate' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $logisticFields);
    }
}
