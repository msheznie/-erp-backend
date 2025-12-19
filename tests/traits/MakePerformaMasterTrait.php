<?php

use Faker\Factory as Faker;
use App\Models\PerformaMaster;
use App\Repositories\PerformaMasterRepository;

trait MakePerformaMasterTrait
{
    /**
     * Create fake instance of PerformaMaster and save it in database
     *
     * @param array $performaMasterFields
     * @return PerformaMaster
     */
    public function makePerformaMaster($performaMasterFields = [])
    {
        /** @var PerformaMasterRepository $performaMasterRepo */
        $performaMasterRepo = App::make(PerformaMasterRepository::class);
        $theme = $this->fakePerformaMasterData($performaMasterFields);
        return $performaMasterRepo->create($theme);
    }

    /**
     * Get fake instance of PerformaMaster
     *
     * @param array $performaMasterFields
     * @return PerformaMaster
     */
    public function fakePerformaMaster($performaMasterFields = [])
    {
        return new PerformaMaster($this->fakePerformaMasterData($performaMasterFields));
    }

    /**
     * Get fake data of PerformaMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakePerformaMasterData($performaMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'PerformaInvoiceNo' => $fake->randomDigitNotNull,
            'performaSerialNO' => $fake->randomDigitNotNull,
            'PerformaCode' => $fake->word,
            'companyID' => $fake->word,
            'serviceLine' => $fake->word,
            'clientID' => $fake->word,
            'contractID' => $fake->word,
            'performaDate' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedUserID' => $fake->word,
            'performaStatus' => $fake->randomDigitNotNull,
            'PerformaOpConfirmed' => $fake->randomDigitNotNull,
            'performaOpConfirmedBy' => $fake->word,
            'performaOpConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'PerformaFinanceConfirmed' => $fake->randomDigitNotNull,
            'performaFinanceConfirmedBy' => $fake->word,
            'performaFinanceConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'performaValue' => $fake->randomDigitNotNull,
            'ticketNo' => $fake->randomDigitNotNull,
            'bankID' => $fake->randomDigitNotNull,
            'accountID' => $fake->randomDigitNotNull,
            'paymentInDaysForJob' => $fake->randomDigitNotNull,
            'custInvNoModified' => $fake->randomDigitNotNull,
            'isPerformaOnEditRental' => $fake->randomDigitNotNull,
            'isRefBackBillingYN' => $fake->randomDigitNotNull,
            'refBackBillingBy' => $fake->word,
            'refBackBillingDate' => $fake->date('Y-m-d H:i:s'),
            'isRefBackOPYN' => $fake->randomDigitNotNull,
            'refBackOPby' => $fake->word,
            'refBackOpDate' => $fake->date('Y-m-d H:i:s'),
            'refBillingComment' => $fake->word,
            'refOpComment' => $fake->word,
            'clientAppPerformaType' => $fake->randomDigitNotNull,
            'clientapprovedDate' => $fake->date('Y-m-d H:i:s'),
            'clientapprovedBy' => $fake->word,
            'performaSentToHO' => $fake->randomDigitNotNull,
            'performaSentToHODate' => $fake->date('Y-m-d H:i:s'),
            'performaSentToHOEmpID' => $fake->word,
            'lotSystemAutoID' => $fake->randomDigitNotNull,
            'lotNumber' => $fake->word,
            'performaReceivedByEmpID' => $fake->word,
            'performaReceivedByDate' => $fake->date('Y-m-d H:i:s'),
            'submittedToClientDate' => $fake->date('Y-m-d H:i:s'),
            'submittedToClientByEmpID' => $fake->word,
            'receivedFromClientDate' => $fake->date('Y-m-d H:i:s'),
            'reSubmittedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedByClientDate' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s'),
            'isAccrualYN' => $fake->randomDigitNotNull,
            'isCanceledYN' => $fake->randomDigitNotNull,
            'serviceCompanyID' => $fake->word
        ], $performaMasterFields);
    }
}
