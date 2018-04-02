<?php

use App\Models\CurrencyConversion;
use App\Repositories\CurrencyConversionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CurrencyConversionRepositoryTest extends TestCase
{
    use MakeCurrencyConversionTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CurrencyConversionRepository
     */
    protected $currencyConversionRepo;

    public function setUp()
    {
        parent::setUp();
        $this->currencyConversionRepo = App::make(CurrencyConversionRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCurrencyConversion()
    {
        $currencyConversion = $this->fakeCurrencyConversionData();
        $createdCurrencyConversion = $this->currencyConversionRepo->create($currencyConversion);
        $createdCurrencyConversion = $createdCurrencyConversion->toArray();
        $this->assertArrayHasKey('id', $createdCurrencyConversion);
        $this->assertNotNull($createdCurrencyConversion['id'], 'Created CurrencyConversion must have id specified');
        $this->assertNotNull(CurrencyConversion::find($createdCurrencyConversion['id']), 'CurrencyConversion with given id must be in DB');
        $this->assertModelData($currencyConversion, $createdCurrencyConversion);
    }

    /**
     * @test read
     */
    public function testReadCurrencyConversion()
    {
        $currencyConversion = $this->makeCurrencyConversion();
        $dbCurrencyConversion = $this->currencyConversionRepo->find($currencyConversion->id);
        $dbCurrencyConversion = $dbCurrencyConversion->toArray();
        $this->assertModelData($currencyConversion->toArray(), $dbCurrencyConversion);
    }

    /**
     * @test update
     */
    public function testUpdateCurrencyConversion()
    {
        $currencyConversion = $this->makeCurrencyConversion();
        $fakeCurrencyConversion = $this->fakeCurrencyConversionData();
        $updatedCurrencyConversion = $this->currencyConversionRepo->update($fakeCurrencyConversion, $currencyConversion->id);
        $this->assertModelData($fakeCurrencyConversion, $updatedCurrencyConversion->toArray());
        $dbCurrencyConversion = $this->currencyConversionRepo->find($currencyConversion->id);
        $this->assertModelData($fakeCurrencyConversion, $dbCurrencyConversion->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCurrencyConversion()
    {
        $currencyConversion = $this->makeCurrencyConversion();
        $resp = $this->currencyConversionRepo->delete($currencyConversion->id);
        $this->assertTrue($resp);
        $this->assertNull(CurrencyConversion::find($currencyConversion->id), 'CurrencyConversion should not exist in DB');
    }
}
