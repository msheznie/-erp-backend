<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\CustomerInvoiceItemDetails;
use App\Repositories\CustomerInvoiceItemDetailsRepository;

trait MakeCustomerInvoiceItemDetailsTrait
{
    /**
     * Create fake instance of CustomerInvoiceItemDetails and save it in database
     *
     * @param array $customerInvoiceItemDetailsFields
     * @return CustomerInvoiceItemDetails
     */
    public function makeCustomerInvoiceItemDetails($customerInvoiceItemDetailsFields = [])
    {
        /** @var CustomerInvoiceItemDetailsRepository $customerInvoiceItemDetailsRepo */
        $customerInvoiceItemDetailsRepo = \App::make(CustomerInvoiceItemDetailsRepository::class);
        $theme = $this->fakeCustomerInvoiceItemDetailsData($customerInvoiceItemDetailsFields);
        return $customerInvoiceItemDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerInvoiceItemDetails
     *
     * @param array $customerInvoiceItemDetailsFields
     * @return CustomerInvoiceItemDetails
     */
    public function fakeCustomerInvoiceItemDetails($customerInvoiceItemDetailsFields = [])
    {
        return new CustomerInvoiceItemDetails($this->fakeCustomerInvoiceItemDetailsData($customerInvoiceItemDetailsFields));
    }

    /**
     * Get fake data of CustomerInvoiceItemDetails
     *
     * @param array $customerInvoiceItemDetailsFields
     * @return array
     */
    public function fakeCustomerInvoiceItemDetailsData($customerInvoiceItemDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'custInvoiceDirectAutoID' => $fake->randomDigitNotNull,
            'itemCodeSystem' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'itemUnitOfMeasure' => $fake->randomDigitNotNull,
            'unitOfMeasureIssued' => $fake->randomDigitNotNull,
            'convertionMeasureVal' => $fake->randomDigitNotNull,
            'qtyIssued' => $fake->randomDigitNotNull,
            'qtyIssuedDefaultMeasure' => $fake->randomDigitNotNull,
            'currentStockQty' => $fake->randomDigitNotNull,
            'currentWareHouseStockQty' => $fake->randomDigitNotNull,
            'currentStockQtyInDamageReturn' => $fake->randomDigitNotNull,
            'comments' => $fake->text,
            'itemFinanceCategoryID' => $fake->randomDigitNotNull,
            'itemFinanceCategorySubID' => $fake->randomDigitNotNull,
            'financeGLcodebBSSystemID' => $fake->randomDigitNotNull,
            'financeGLcodebBS' => $fake->word,
            'financeGLcodePLSystemID' => $fake->randomDigitNotNull,
            'financeGLcodePL' => $fake->word,
            'includePLForGRVYN' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'issueCostLocal' => $fake->randomDigitNotNull,
            'issueCostLocalTotal' => $fake->randomDigitNotNull,
            'reportingCurrencyID' => $fake->randomDigitNotNull,
            'reportingCurrencyER' => $fake->randomDigitNotNull,
            'issueCostRpt' => $fake->randomDigitNotNull,
            'issueCostRptTotal' => $fake->randomDigitNotNull,
            'marginPercentage' => $fake->randomDigitNotNull,
            'sellingCurrencyID' => $fake->randomDigitNotNull,
            'sellingCurrencyER' => $fake->randomDigitNotNull,
            'sellingCost' => $fake->randomDigitNotNull,
            'sellingCostAfterMargin' => $fake->randomDigitNotNull,
            'sellingTotal' => $fake->randomDigitNotNull,
            'sellingCostAfterMarginLocal' => $fake->randomDigitNotNull,
            'sellingCostAfterMarginRpt' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $customerInvoiceItemDetailsFields);
    }
}
