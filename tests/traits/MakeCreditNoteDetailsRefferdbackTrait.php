<?php

use Faker\Factory as Faker;
use App\Models\CreditNoteDetailsRefferdback;
use App\Repositories\CreditNoteDetailsRefferdbackRepository;

trait MakeCreditNoteDetailsRefferdbackTrait
{
    /**
     * Create fake instance of CreditNoteDetailsRefferdback and save it in database
     *
     * @param array $creditNoteDetailsRefferdbackFields
     * @return CreditNoteDetailsRefferdback
     */
    public function makeCreditNoteDetailsRefferdback($creditNoteDetailsRefferdbackFields = [])
    {
        /** @var CreditNoteDetailsRefferdbackRepository $creditNoteDetailsRefferdbackRepo */
        $creditNoteDetailsRefferdbackRepo = App::make(CreditNoteDetailsRefferdbackRepository::class);
        $theme = $this->fakeCreditNoteDetailsRefferdbackData($creditNoteDetailsRefferdbackFields);
        return $creditNoteDetailsRefferdbackRepo->create($theme);
    }

    /**
     * Get fake instance of CreditNoteDetailsRefferdback
     *
     * @param array $creditNoteDetailsRefferdbackFields
     * @return CreditNoteDetailsRefferdback
     */
    public function fakeCreditNoteDetailsRefferdback($creditNoteDetailsRefferdbackFields = [])
    {
        return new CreditNoteDetailsRefferdback($this->fakeCreditNoteDetailsRefferdbackData($creditNoteDetailsRefferdbackFields));
    }

    /**
     * Get fake data of CreditNoteDetailsRefferdback
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCreditNoteDetailsRefferdbackData($creditNoteDetailsRefferdbackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'creditNoteDetailsID' => $fake->randomDigitNotNull,
            'creditNoteAutoID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'customerID' => $fake->randomDigitNotNull,
            'glCode' => $fake->word,
            'glCodeDes' => $fake->word,
            'serviceLineCode' => $fake->word,
            'comments' => $fake->word,
            'creditAmountCurrency' => $fake->randomDigitNotNull,
            'creditAmountCurrencyER' => $fake->randomDigitNotNull,
            'creditAmount' => $fake->randomDigitNotNull,
            'localCurrency' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'comRptCurrency' => $fake->randomDigitNotNull,
            'comRptCurrencyER' => $fake->randomDigitNotNull,
            'comRptAmount' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $creditNoteDetailsRefferdbackFields);
    }
}
