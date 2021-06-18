<?php namespace Tests\Repositories;

use App\Models\CurrencyConversionDetail;
use App\Repositories\CurrencyConversionDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CurrencyConversionDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CurrencyConversionDetailRepository
     */
    protected $currencyConversionDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->currencyConversionDetailRepo = \App::make(CurrencyConversionDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_currency_conversion_detail()
    {
        $currencyConversionDetail = factory(CurrencyConversionDetail::class)->make()->toArray();

        $createdCurrencyConversionDetail = $this->currencyConversionDetailRepo->create($currencyConversionDetail);

        $createdCurrencyConversionDetail = $createdCurrencyConversionDetail->toArray();
        $this->assertArrayHasKey('id', $createdCurrencyConversionDetail);
        $this->assertNotNull($createdCurrencyConversionDetail['id'], 'Created CurrencyConversionDetail must have id specified');
        $this->assertNotNull(CurrencyConversionDetail::find($createdCurrencyConversionDetail['id']), 'CurrencyConversionDetail with given id must be in DB');
        $this->assertModelData($currencyConversionDetail, $createdCurrencyConversionDetail);
    }

    /**
     * @test read
     */
    public function test_read_currency_conversion_detail()
    {
        $currencyConversionDetail = factory(CurrencyConversionDetail::class)->create();

        $dbCurrencyConversionDetail = $this->currencyConversionDetailRepo->find($currencyConversionDetail->id);

        $dbCurrencyConversionDetail = $dbCurrencyConversionDetail->toArray();
        $this->assertModelData($currencyConversionDetail->toArray(), $dbCurrencyConversionDetail);
    }

    /**
     * @test update
     */
    public function test_update_currency_conversion_detail()
    {
        $currencyConversionDetail = factory(CurrencyConversionDetail::class)->create();
        $fakeCurrencyConversionDetail = factory(CurrencyConversionDetail::class)->make()->toArray();

        $updatedCurrencyConversionDetail = $this->currencyConversionDetailRepo->update($fakeCurrencyConversionDetail, $currencyConversionDetail->id);

        $this->assertModelData($fakeCurrencyConversionDetail, $updatedCurrencyConversionDetail->toArray());
        $dbCurrencyConversionDetail = $this->currencyConversionDetailRepo->find($currencyConversionDetail->id);
        $this->assertModelData($fakeCurrencyConversionDetail, $dbCurrencyConversionDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_currency_conversion_detail()
    {
        $currencyConversionDetail = factory(CurrencyConversionDetail::class)->create();

        $resp = $this->currencyConversionDetailRepo->delete($currencyConversionDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(CurrencyConversionDetail::find($currencyConversionDetail->id), 'CurrencyConversionDetail should not exist in DB');
    }
}
