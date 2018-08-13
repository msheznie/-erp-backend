<?php

use Faker\Factory as Faker;
use App\Models\StockTransfer;
use App\Repositories\StockTransferRepository;

trait MakeStockTransferTrait
{
    /**
     * Create fake instance of StockTransfer and save it in database
     *
     * @param array $stockTransferFields
     * @return StockTransfer
     */
    public function makeStockTransfer($stockTransferFields = [])
    {
        /** @var StockTransferRepository $stockTransferRepo */
        $stockTransferRepo = App::make(StockTransferRepository::class);
        $theme = $this->fakeStockTransferData($stockTransferFields);
        return $stockTransferRepo->create($theme);
    }

    /**
     * Get fake instance of StockTransfer
     *
     * @param array $stockTransferFields
     * @return StockTransfer
     */
    public function fakeStockTransfer($stockTransferFields = [])
    {
        return new StockTransfer($this->fakeStockTransferData($stockTransferFields));
    }

    /**
     * Get fake data of StockTransfer
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStockTransferData($stockTransferFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyID' => $fake->word,
            'serviceLineCode' => $fake->word,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'documentID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'stockTransferCode' => $fake->word,
            'refNo' => $fake->word,
            'tranferDate' => $fake->date('Y-m-d H:i:s'),
            'comment' => $fake->word,
            'companyFrom' => $fake->word,
            'companyTo' => $fake->word,
            'locationTo' => $fake->randomDigitNotNull,
            'locationFrom' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'fullyReceived' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'interCompanyTransferYN' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserGroup' => $fake->word,
            'createdPCID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $stockTransferFields);
    }
}
