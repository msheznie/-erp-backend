<?php

use Faker\Factory as Faker;
use App\Models\MatchDocumentMaster;
use App\Repositories\MatchDocumentMasterRepository;

trait MakeMatchDocumentMasterTrait
{
    /**
     * Create fake instance of MatchDocumentMaster and save it in database
     *
     * @param array $matchDocumentMasterFields
     * @return MatchDocumentMaster
     */
    public function makeMatchDocumentMaster($matchDocumentMasterFields = [])
    {
        /** @var MatchDocumentMasterRepository $matchDocumentMasterRepo */
        $matchDocumentMasterRepo = App::make(MatchDocumentMasterRepository::class);
        $theme = $this->fakeMatchDocumentMasterData($matchDocumentMasterFields);
        return $matchDocumentMasterRepo->create($theme);
    }

    /**
     * Get fake instance of MatchDocumentMaster
     *
     * @param array $matchDocumentMasterFields
     * @return MatchDocumentMaster
     */
    public function fakeMatchDocumentMaster($matchDocumentMasterFields = [])
    {
        return new MatchDocumentMaster($this->fakeMatchDocumentMasterData($matchDocumentMasterFields));
    }

    /**
     * Get fake data of MatchDocumentMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeMatchDocumentMasterData($matchDocumentMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'PayMasterAutoId' => $fake->randomDigitNotNull,
            'documentSystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'matchingDocCode' => $fake->word,
            'matchingDocdate' => $fake->date('Y-m-d H:i:s'),
            'BPVcode' => $fake->word,
            'BPVdate' => $fake->date('Y-m-d H:i:s'),
            'BPVNarration' => $fake->word,
            'directPaymentPayee' => $fake->word,
            'directPayeeCurrency' => $fake->randomDigitNotNull,
            'BPVsupplierID' => $fake->randomDigitNotNull,
            'supplierGLCode' => $fake->word,
            'supplierTransCurrencyID' => $fake->randomDigitNotNull,
            'supplierTransCurrencyER' => $fake->randomDigitNotNull,
            'supplierDefCurrencyID' => $fake->randomDigitNotNull,
            'supplierDefCurrencyER' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'companyRptCurrencyID' => $fake->randomDigitNotNull,
            'companyRptCurrencyER' => $fake->randomDigitNotNull,
            'payAmountBank' => $fake->randomDigitNotNull,
            'payAmountSuppTrans' => $fake->randomDigitNotNull,
            'payAmountSuppDef' => $fake->randomDigitNotNull,
            'suppAmountDocTotal' => $fake->randomDigitNotNull,
            'payAmountCompLocal' => $fake->randomDigitNotNull,
            'payAmountCompRpt' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'invoiceType' => $fake->randomDigitNotNull,
            'matchInvoice' => $fake->randomDigitNotNull,
            'matchingConfirmedYN' => $fake->randomDigitNotNull,
            'matchingConfirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'matchingConfirmedByEmpID' => $fake->word,
            'matchingConfirmedByName' => $fake->word,
            'matchingConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'matchingAmount' => $fake->randomDigitNotNull,
            'matchBalanceAmount' => $fake->randomDigitNotNull,
            'matchedAmount' => $fake->randomDigitNotNull,
            'matchLocalAmount' => $fake->randomDigitNotNull,
            'matchRptAmount' => $fake->randomDigitNotNull,
            'matchingType' => $fake->word,
            'isExchangematch' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $matchDocumentMasterFields);
    }
}
