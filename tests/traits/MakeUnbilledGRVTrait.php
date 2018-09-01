<?php

use Faker\Factory as Faker;
use App\Models\UnbilledGRV;
use App\Repositories\UnbilledGRVRepository;

trait MakeUnbilledGRVTrait
{
    /**
     * Create fake instance of UnbilledGRV and save it in database
     *
     * @param array $unbilledGRVFields
     * @return UnbilledGRV
     */
    public function makeUnbilledGRV($unbilledGRVFields = [])
    {
        /** @var UnbilledGRVRepository $unbilledGRVRepo */
        $unbilledGRVRepo = App::make(UnbilledGRVRepository::class);
        $theme = $this->fakeUnbilledGRVData($unbilledGRVFields);
        return $unbilledGRVRepo->create($theme);
    }

    /**
     * Get fake instance of UnbilledGRV
     *
     * @param array $unbilledGRVFields
     * @return UnbilledGRV
     */
    public function fakeUnbilledGRV($unbilledGRVFields = [])
    {
        return new UnbilledGRV($this->fakeUnbilledGRVData($unbilledGRVFields));
    }

    /**
     * Get fake data of UnbilledGRV
     *
     * @param array $postFields
     * @return array
     */
    public function fakeUnbilledGRVData($unbilledGRVFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyID' => $fake->word,
            'supplierID' => $fake->randomDigitNotNull,
            'purchaseOrderID' => $fake->randomDigitNotNull,
            'grvAutoID' => $fake->randomDigitNotNull,
            'grvDate' => $fake->date('Y-m-d H:i:s'),
            'supplierTransactionCurrencyID' => $fake->randomDigitNotNull,
            'supplierTransactionCurrencyER' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingER' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'totTransactionAmount' => $fake->randomDigitNotNull,
            'totLocalAmount' => $fake->randomDigitNotNull,
            'totRptAmount' => $fake->randomDigitNotNull,
            'isAddon' => $fake->randomDigitNotNull,
            'grvType' => $fake->word,
            'isReturn' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $unbilledGRVFields);
    }
}
