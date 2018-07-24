<?php

use Faker\Factory as Faker;
use App\Models\UnbilledGrvGroupBy;
use App\Repositories\UnbilledGrvGroupByRepository;

trait MakeUnbilledGrvGroupByTrait
{
    /**
     * Create fake instance of UnbilledGrvGroupBy and save it in database
     *
     * @param array $unbilledGrvGroupByFields
     * @return UnbilledGrvGroupBy
     */
    public function makeUnbilledGrvGroupBy($unbilledGrvGroupByFields = [])
    {
        /** @var UnbilledGrvGroupByRepository $unbilledGrvGroupByRepo */
        $unbilledGrvGroupByRepo = App::make(UnbilledGrvGroupByRepository::class);
        $theme = $this->fakeUnbilledGrvGroupByData($unbilledGrvGroupByFields);
        return $unbilledGrvGroupByRepo->create($theme);
    }

    /**
     * Get fake instance of UnbilledGrvGroupBy
     *
     * @param array $unbilledGrvGroupByFields
     * @return UnbilledGrvGroupBy
     */
    public function fakeUnbilledGrvGroupBy($unbilledGrvGroupByFields = [])
    {
        return new UnbilledGrvGroupBy($this->fakeUnbilledGrvGroupByData($unbilledGrvGroupByFields));
    }

    /**
     * Get fake data of UnbilledGrvGroupBy
     *
     * @param array $postFields
     * @return array
     */
    public function fakeUnbilledGrvGroupByData($unbilledGrvGroupByFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
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
            'selectedForBooking' => $fake->randomDigitNotNull,
            'fullyBooked' => $fake->randomDigitNotNull,
            'grvType' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $unbilledGrvGroupByFields);
    }
}
