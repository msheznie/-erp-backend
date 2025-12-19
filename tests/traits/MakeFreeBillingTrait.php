<?php

use Faker\Factory as Faker;
use App\Models\FreeBilling;
use App\Repositories\FreeBillingRepository;

trait MakeFreeBillingTrait
{
    /**
     * Create fake instance of FreeBilling and save it in database
     *
     * @param array $freeBillingFields
     * @return FreeBilling
     */
    public function makeFreeBilling($freeBillingFields = [])
    {
        /** @var FreeBillingRepository $freeBillingRepo */
        $freeBillingRepo = App::make(FreeBillingRepository::class);
        $theme = $this->fakeFreeBillingData($freeBillingFields);
        return $freeBillingRepo->create($theme);
    }

    /**
     * Get fake instance of FreeBilling
     *
     * @param array $freeBillingFields
     * @return FreeBilling
     */
    public function fakeFreeBilling($freeBillingFields = [])
    {
        return new FreeBilling($this->fakeFreeBillingData($freeBillingFields));
    }

    /**
     * Get fake data of FreeBilling
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFreeBillingData($freeBillingFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'billProcessNo' => $fake->randomDigitNotNull,
            'TicketNo' => $fake->randomDigitNotNull,
            'motID' => $fake->randomDigitNotNull,
            'mitID' => $fake->randomDigitNotNull,
            'AssetUnitID' => $fake->randomDigitNotNull,
            'assetSerialNo' => $fake->word,
            'unitID' => $fake->randomDigitNotNull,
            'rateCurrencyID' => $fake->randomDigitNotNull,
            'StandardTimeOnLoc' => $fake->randomDigitNotNull,
            'StandardTimeOnLocInitial' => $fake->randomDigitNotNull,
            'standardRate' => $fake->randomDigitNotNull,
            'operationTimeOnLoc' => $fake->randomDigitNotNull,
            'operationTimeOnLocInitial' => $fake->randomDigitNotNull,
            'operationRate' => $fake->randomDigitNotNull,
            'UsageTimeOnLoc' => $fake->randomDigitNotNull,
            'UsageTimeOnLocInitial' => $fake->randomDigitNotNull,
            'usageRate' => $fake->randomDigitNotNull,
            'lostInHoleYN' => $fake->randomDigitNotNull,
            'lostInHoleYNinitial' => $fake->randomDigitNotNull,
            'lostInHoleRate' => $fake->randomDigitNotNull,
            'lihDate' => $fake->date('Y-m-d H:i:s'),
            'dbrYN' => $fake->randomDigitNotNull,
            'dbrYNinitial' => $fake->randomDigitNotNull,
            'dbrRate' => $fake->randomDigitNotNull,
            'performaInvoiceNo' => $fake->randomDigitNotNull,
            'InvoiceNo' => $fake->randomDigitNotNull,
            'usedYN' => $fake->randomDigitNotNull,
            'usedYNinitial' => $fake->randomDigitNotNull,
            'ContractDetailID' => $fake->randomDigitNotNull,
            'lihInspectionStartedYN' => $fake->randomDigitNotNull,
            'dbrInspectionStartedYN' => $fake->randomDigitNotNull,
            'mitQty' => $fake->randomDigitNotNull,
            'assetDescription' => $fake->word,
            'motDate' => $fake->date('Y-m-d H:i:s'),
            'mitDate' => $fake->date('Y-m-d H:i:s'),
            'rentalStartDate' => $fake->date('Y-m-d H:i:s'),
            'rentalEndDate' => $fake->date('Y-m-d H:i:s'),
            'assetDescriptionAmend' => $fake->word,
            'amendHistory' => $fake->text,
            'stdGLcode' => $fake->word,
            'operatingGLcode' => $fake->word,
            'usageGLcode' => $fake->word,
            'lihGLcode' => $fake->word,
            'dbrGLcode' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'qtyServiceProduct' => $fake->randomDigitNotNull,
            'opPerformaCaptionLink' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s'),
            'unitOP' => $fake->randomDigitNotNull,
            'unitUsage' => $fake->randomDigitNotNull,
            'unitLIH' => $fake->randomDigitNotNull,
            'unitDBR' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLine' => $fake->word,
            'UsageLinkID' => $fake->randomDigitNotNull,
            'subContDetID' => $fake->randomDigitNotNull,
            'subContDetails' => $fake->word,
            'usageType' => $fake->randomDigitNotNull,
            'usageTypeDes' => $fake->word,
            'ticketDetDes' => $fake->word,
            'groupOnRptYN' => $fake->randomDigitNotNull,
            'isConsumable' => $fake->randomDigitNotNull,
            'motDetailID' => $fake->randomDigitNotNull,
            'freeBillingComment' => $fake->word,
            'StbHrRate' => $fake->randomDigitNotNull,
            'OpHrRate' => $fake->randomDigitNotNull
        ], $freeBillingFields);
    }
}
