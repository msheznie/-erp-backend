<?php

use Faker\Factory as Faker;
use App\Models\ErpItemLedger;
use App\Repositories\ErpItemLedgerRepository;

trait MakeErpItemLedgerTrait
{
    /**
     * Create fake instance of ErpItemLedger and save it in database
     *
     * @param array $erpItemLedgerFields
     * @return ErpItemLedger
     */
    public function makeErpItemLedger($erpItemLedgerFields = [])
    {
        /** @var ErpItemLedgerRepository $erpItemLedgerRepo */
        $erpItemLedgerRepo = App::make(ErpItemLedgerRepository::class);
        $theme = $this->fakeErpItemLedgerData($erpItemLedgerFields);
        return $erpItemLedgerRepo->create($theme);
    }

    /**
     * Get fake instance of ErpItemLedger
     *
     * @param array $erpItemLedgerFields
     * @return ErpItemLedger
     */
    public function fakeErpItemLedger($erpItemLedgerFields = [])
    {
        return new ErpItemLedger($this->fakeErpItemLedgerData($erpItemLedgerFields));
    }

    /**
     * Get fake data of ErpItemLedger
     *
     * @param array $postFields
     * @return array
     */
    public function fakeErpItemLedgerData($erpItemLedgerFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'documentSystemCode' => $fake->randomDigitNotNull,
            'documentCode' => $fake->word,
            'referenceNumber' => $fake->word,
            'wareHouseSystemCode' => $fake->randomDigitNotNull,
            'itemSystemCode' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'unitOfMeasure' => $fake->randomDigitNotNull,
            'inOutQty' => $fake->randomDigitNotNull,
            'wacLocalCurrencyID' => $fake->randomDigitNotNull,
            'wacLocal' => $fake->randomDigitNotNull,
            'wacRptCurrencyID' => $fake->randomDigitNotNull,
            'wacRpt' => $fake->randomDigitNotNull,
            'comments' => $fake->text,
            'transactionDate' => $fake->date('Y-m-d H:i:s'),
            'fromDamagedTransactionYN' => $fake->randomDigitNotNull,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $erpItemLedgerFields);
    }
}
