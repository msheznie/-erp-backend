<?php

use Faker\Factory as Faker;
use App\Models\DirectInvoiceDetailsRefferedBack;
use App\Repositories\DirectInvoiceDetailsRefferedBackRepository;

trait MakeDirectInvoiceDetailsRefferedBackTrait
{
    /**
     * Create fake instance of DirectInvoiceDetailsRefferedBack and save it in database
     *
     * @param array $directInvoiceDetailsRefferedBackFields
     * @return DirectInvoiceDetailsRefferedBack
     */
    public function makeDirectInvoiceDetailsRefferedBack($directInvoiceDetailsRefferedBackFields = [])
    {
        /** @var DirectInvoiceDetailsRefferedBackRepository $directInvoiceDetailsRefferedBackRepo */
        $directInvoiceDetailsRefferedBackRepo = App::make(DirectInvoiceDetailsRefferedBackRepository::class);
        $theme = $this->fakeDirectInvoiceDetailsRefferedBackData($directInvoiceDetailsRefferedBackFields);
        return $directInvoiceDetailsRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of DirectInvoiceDetailsRefferedBack
     *
     * @param array $directInvoiceDetailsRefferedBackFields
     * @return DirectInvoiceDetailsRefferedBack
     */
    public function fakeDirectInvoiceDetailsRefferedBack($directInvoiceDetailsRefferedBackFields = [])
    {
        return new DirectInvoiceDetailsRefferedBack($this->fakeDirectInvoiceDetailsRefferedBackData($directInvoiceDetailsRefferedBackFields));
    }

    /**
     * Get fake data of DirectInvoiceDetailsRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDirectInvoiceDetailsRefferedBackData($directInvoiceDetailsRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'directInvoiceDetailsID' => $fake->randomDigitNotNull,
            'directInvoiceAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'chartOfAccountSystemID' => $fake->randomDigitNotNull,
            'glCode' => $fake->word,
            'glCodeDes' => $fake->word,
            'comments' => $fake->word,
            'percentage' => $fake->randomDigitNotNull,
            'DIAmountCurrency' => $fake->randomDigitNotNull,
            'DIAmountCurrencyER' => $fake->randomDigitNotNull,
            'DIAmount' => $fake->randomDigitNotNull,
            'localCurrency' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'comRptCurrency' => $fake->randomDigitNotNull,
            'comRptCurrencyER' => $fake->randomDigitNotNull,
            'comRptAmount' => $fake->randomDigitNotNull,
            'budgetYear' => $fake->randomDigitNotNull,
            'isExtraAddon' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $directInvoiceDetailsRefferedBackFields);
    }
}
