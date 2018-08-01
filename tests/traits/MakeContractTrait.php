<?php

use Faker\Factory as Faker;
use App\Models\Contract;
use App\Repositories\ContractRepository;

trait MakeContractTrait
{
    /**
     * Create fake instance of Contract and save it in database
     *
     * @param array $contractFields
     * @return Contract
     */
    public function makeContract($contractFields = [])
    {
        /** @var ContractRepository $contractRepo */
        $contractRepo = App::make(ContractRepository::class);
        $theme = $this->fakeContractData($contractFields);
        return $contractRepo->create($theme);
    }

    /**
     * Get fake instance of Contract
     *
     * @param array $contractFields
     * @return Contract
     */
    public function fakeContract($contractFields = [])
    {
        return new Contract($this->fakeContractData($contractFields));
    }

    /**
     * Get fake data of Contract
     *
     * @param array $postFields
     * @return array
     */
    public function fakeContractData($contractFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'ContractNumber' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'CompanyID' => $fake->word,
            'clientID' => $fake->randomDigitNotNull,
            'CutomerCode' => $fake->word,
            'ServiceLineCode' => $fake->word,
            'contractDescription' => $fake->word,
            'ContStartDate' => $fake->date('Y-m-d H:i:s'),
            'ContEndDate' => $fake->date('Y-m-d H:i:s'),
            'ContCurrencyID' => $fake->randomDigitNotNull,
            'contValue' => $fake->randomDigitNotNull,
            'isInitialExtCont' => $fake->randomDigitNotNull,
            'ContExtUpto' => $fake->date('Y-m-d H:i:s'),
            'LineTechnicalIncharge' => $fake->word,
            'LineFinanceIncharge' => $fake->word,
            'LineOthersIncharge' => $fake->word,
            'ContractCreatedON' => $fake->date('Y-m-d H:i:s'),
            'createdPcID' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'allowMultipleBillingYN' => $fake->randomDigitNotNull,
            'isContract' => $fake->randomDigitNotNull,
            'allowRentalWithoutMITyn' => $fake->randomDigitNotNull,
            'allowEditRentalDes' => $fake->randomDigitNotNull,
            'defaultRateInRental' => $fake->randomDigitNotNull,
            'allowEditUOM' => $fake->randomDigitNotNull,
            'rentalTemplate' => $fake->word,
            'contractType' => $fake->randomDigitNotNull,
            'contractSubType' => $fake->randomDigitNotNull,
            'bankID' => $fake->randomDigitNotNull,
            'accountID' => $fake->randomDigitNotNull,
            'vendonCode' => $fake->word,
            'paymentInDaysForJob' => $fake->randomDigitNotNull,
            'ticketClientSerialStart' => $fake->randomDigitNotNull,
            'secondaryLogoComp' => $fake->word,
            'secondaryLogName' => $fake->word,
            'secondaryLogoActive' => $fake->randomDigitNotNull,
            'estRevServiceGLcode' => $fake->word,
            'estRevProductGLcode' => $fake->word,
            'isFormulaApplicable' => $fake->randomDigitNotNull,
            'opHrsRounding' => $fake->randomDigitNotNull,
            'formulaOphrsFromField' => $fake->word,
            'formulaOphrsToField' => $fake->word,
            'formulaStandbyField' => $fake->word,
            'isStandByApplicable' => $fake->randomDigitNotNull,
            'customerRepName' => $fake->word,
            'customerRepEmail' => $fake->word,
            'showContDetInMOT' => $fake->randomDigitNotNull,
            'showContDetInMIT' => $fake->randomDigitNotNull,
            'performaTempID' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s'),
            'contInvTemplate' => $fake->word,
            'isAllowGenerateTransRental' => $fake->randomDigitNotNull,
            'isAllowServiceEntryInPerforma' => $fake->randomDigitNotNull,
            'isAllowedDefauldUsage' => $fake->randomDigitNotNull,
            'actionTrackerEnabled' => $fake->randomDigitNotNull,
            'webTemplate' => $fake->word,
            'isRequiredStamp' => $fake->randomDigitNotNull,
            'showSystemNo' => $fake->randomDigitNotNull,
            'isAllowedToolsWithoutMOT' => $fake->randomDigitNotNull,
            'isDispacthAvailable' => $fake->randomDigitNotNull,
            'isRequireAppNewWell' => $fake->randomDigitNotNull,
            'isMorningReportAvailable' => $fake->randomDigitNotNull,
            'isContractActive' => $fake->randomDigitNotNull,
            'allowMutipleTicketsInProforma' => $fake->randomDigitNotNull,
            'isServiceEntryApplicable' => $fake->randomDigitNotNull,
            'isTicketKPIApplicable' => $fake->randomDigitNotNull,
            'isTicketTotalApplicable' => $fake->randomDigitNotNull,
            'isMotAssetDescEditable' => $fake->randomDigitNotNull,
            'motTemplate' => $fake->word,
            'mitTemplate' => $fake->word,
            'rentalDates' => $fake->randomDigitNotNull,
            'invoiceTemplate' => $fake->randomDigitNotNull,
            'rentalSheetTemplate' => $fake->word,
            'isRequiredNetworkRefNo' => $fake->randomDigitNotNull,
            'formulaLocHrsFromField' => $fake->word,
            'formulaLocHrsToField' => $fake->word,
            'isServiceApplicable' => $fake->randomDigitNotNull,
            'isAllowToEditHours' => $fake->randomDigitNotNull,
            'contractStatus' => $fake->randomDigitNotNull,
            'ticketTemplates' => $fake->word,
            'allowOpStdyDaysinMIT' => $fake->randomDigitNotNull,
            'motprintTemplate' => $fake->randomDigitNotNull
        ], $contractFields);
    }
}
