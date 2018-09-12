<?php

use Faker\Factory as Faker;
use App\Models\LogisticDetails;
use App\Repositories\LogisticDetailsRepository;

trait MakeLogisticDetailsTrait
{
    /**
     * Create fake instance of LogisticDetails and save it in database
     *
     * @param array $logisticDetailsFields
     * @return LogisticDetails
     */
    public function makeLogisticDetails($logisticDetailsFields = [])
    {
        /** @var LogisticDetailsRepository $logisticDetailsRepo */
        $logisticDetailsRepo = App::make(LogisticDetailsRepository::class);
        $theme = $this->fakeLogisticDetailsData($logisticDetailsFields);
        return $logisticDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of LogisticDetails
     *
     * @param array $logisticDetailsFields
     * @return LogisticDetails
     */
    public function fakeLogisticDetails($logisticDetailsFields = [])
    {
        return new LogisticDetails($this->fakeLogisticDetailsData($logisticDetailsFields));
    }

    /**
     * Get fake data of LogisticDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeLogisticDetailsData($logisticDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'logisticMasterID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'supplierID' => $fake->randomDigitNotNull,
            'POid' => $fake->randomDigitNotNull,
            'POdetailID' => $fake->randomDigitNotNull,
            'itemcodeSystem' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'partNo' => $fake->word,
            'itemUOM' => $fake->randomDigitNotNull,
            'itemPOQtry' => $fake->randomDigitNotNull,
            'itemShippingQty' => $fake->randomDigitNotNull,
            'POdeliveryWarehousLocation' => $fake->randomDigitNotNull,
            'GRVStatus' => $fake->randomDigitNotNull,
            'GRVsystemCode' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $logisticDetailsFields);
    }
}
