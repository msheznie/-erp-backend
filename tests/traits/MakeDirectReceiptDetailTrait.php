<?php

use Faker\Factory as Faker;
use App\Models\DirectReceiptDetail;
use App\Repositories\DirectReceiptDetailRepository;

trait MakeDirectReceiptDetailTrait
{
    /**
     * Create fake instance of DirectReceiptDetail and save it in database
     *
     * @param array $directReceiptDetailFields
     * @return DirectReceiptDetail
     */
    public function makeDirectReceiptDetail($directReceiptDetailFields = [])
    {
        /** @var DirectReceiptDetailRepository $directReceiptDetailRepo */
        $directReceiptDetailRepo = App::make(DirectReceiptDetailRepository::class);
        $theme = $this->fakeDirectReceiptDetailData($directReceiptDetailFields);
        return $directReceiptDetailRepo->create($theme);
    }

    /**
     * Get fake instance of DirectReceiptDetail
     *
     * @param array $directReceiptDetailFields
     * @return DirectReceiptDetail
     */
    public function fakeDirectReceiptDetail($directReceiptDetailFields = [])
    {
        return new DirectReceiptDetail($this->fakeDirectReceiptDetailData($directReceiptDetailFields));
    }

    /**
     * Get fake data of DirectReceiptDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDirectReceiptDetailData($directReceiptDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'directReceiptAutoID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineCode' => $fake->word,
            'glCode' => $fake->word,
            'glCodeDes' => $fake->word,
            'contractID' => $fake->word,
            'comments' => $fake->word,
            'DRAmountCurrency' => $fake->randomDigitNotNull,
            'DDRAmountCurrencyER' => $fake->randomDigitNotNull,
            'DRAmount' => $fake->randomDigitNotNull,
            'localCurrency' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'comRptCurrency' => $fake->randomDigitNotNull,
            'comRptCurrencyER' => $fake->randomDigitNotNull,
            'comRptAmount' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $directReceiptDetailFields);
    }
}
