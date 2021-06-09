<?php namespace Tests\Repositories;

use App\Models\CurrencyConversionMaster;
use App\Repositories\CurrencyConversionMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CurrencyConversionMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CurrencyConversionMasterRepository
     */
    protected $currencyConversionMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->currencyConversionMasterRepo = \App::make(CurrencyConversionMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_currency_conversion_master()
    {
        $currencyConversionMaster = factory(CurrencyConversionMaster::class)->make()->toArray();

        $createdCurrencyConversionMaster = $this->currencyConversionMasterRepo->create($currencyConversionMaster);

        $createdCurrencyConversionMaster = $createdCurrencyConversionMaster->toArray();
        $this->assertArrayHasKey('id', $createdCurrencyConversionMaster);
        $this->assertNotNull($createdCurrencyConversionMaster['id'], 'Created CurrencyConversionMaster must have id specified');
        $this->assertNotNull(CurrencyConversionMaster::find($createdCurrencyConversionMaster['id']), 'CurrencyConversionMaster with given id must be in DB');
        $this->assertModelData($currencyConversionMaster, $createdCurrencyConversionMaster);
    }

    /**
     * @test read
     */
    public function test_read_currency_conversion_master()
    {
        $currencyConversionMaster = factory(CurrencyConversionMaster::class)->create();

        $dbCurrencyConversionMaster = $this->currencyConversionMasterRepo->find($currencyConversionMaster->id);

        $dbCurrencyConversionMaster = $dbCurrencyConversionMaster->toArray();
        $this->assertModelData($currencyConversionMaster->toArray(), $dbCurrencyConversionMaster);
    }

    /**
     * @test update
     */
    public function test_update_currency_conversion_master()
    {
        $currencyConversionMaster = factory(CurrencyConversionMaster::class)->create();
        $fakeCurrencyConversionMaster = factory(CurrencyConversionMaster::class)->make()->toArray();

        $updatedCurrencyConversionMaster = $this->currencyConversionMasterRepo->update($fakeCurrencyConversionMaster, $currencyConversionMaster->id);

        $this->assertModelData($fakeCurrencyConversionMaster, $updatedCurrencyConversionMaster->toArray());
        $dbCurrencyConversionMaster = $this->currencyConversionMasterRepo->find($currencyConversionMaster->id);
        $this->assertModelData($fakeCurrencyConversionMaster, $dbCurrencyConversionMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_currency_conversion_master()
    {
        $currencyConversionMaster = factory(CurrencyConversionMaster::class)->create();

        $resp = $this->currencyConversionMasterRepo->delete($currencyConversionMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(CurrencyConversionMaster::find($currencyConversionMaster->id), 'CurrencyConversionMaster should not exist in DB');
    }
}
