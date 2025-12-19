<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\SupplierCatalogDetail;
use App\Repositories\SupplierCatalogDetailRepository;

trait MakeSupplierCatalogDetailTrait
{
    /**
     * Create fake instance of SupplierCatalogDetail and save it in database
     *
     * @param array $supplierCatalogDetailFields
     * @return SupplierCatalogDetail
     */
    public function makeSupplierCatalogDetail($supplierCatalogDetailFields = [])
    {
        /** @var SupplierCatalogDetailRepository $supplierCatalogDetailRepo */
        $supplierCatalogDetailRepo = \App::make(SupplierCatalogDetailRepository::class);
        $theme = $this->fakeSupplierCatalogDetailData($supplierCatalogDetailFields);
        return $supplierCatalogDetailRepo->create($theme);
    }

    /**
     * Get fake instance of SupplierCatalogDetail
     *
     * @param array $supplierCatalogDetailFields
     * @return SupplierCatalogDetail
     */
    public function fakeSupplierCatalogDetail($supplierCatalogDetailFields = [])
    {
        return new SupplierCatalogDetail($this->fakeSupplierCatalogDetailData($supplierCatalogDetailFields));
    }

    /**
     * Get fake data of SupplierCatalogDetail
     *
     * @param array $supplierCatalogDetailFields
     * @return array
     */
    public function fakeSupplierCatalogDetailData($supplierCatalogDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'supplierCatalogMasterID' => $fake->randomDigitNotNull,
            'itemCodeSystem' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'itemUnitOfMeasure' => $fake->randomDigitNotNull,
            'partNo' => $fake->word,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localPrice' => $fake->word,
            'reportingCurrencyID' => $fake->randomDigitNotNull,
            'reportingPrice' => $fake->word,
            'leadTime' => $fake->randomDigitNotNull,
            'timstamp' => $fake->date('Y-m-d H:i:s')
        ], $supplierCatalogDetailFields);
    }
}
