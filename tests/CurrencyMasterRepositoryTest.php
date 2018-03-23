<?php

use App\Models\CurrencyMaster;
use App\Repositories\CurrencyMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CurrencyMasterRepositoryTest extends TestCase
{
    use MakeCurrencyMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CurrencyMasterRepository
     */
    protected $currencyMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->currencyMasterRepo = App::make(CurrencyMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCurrencyMaster()
    {
        $currencyMaster = $this->fakeCurrencyMasterData();
        $createdCurrencyMaster = $this->currencyMasterRepo->create($currencyMaster);
        $createdCurrencyMaster = $createdCurrencyMaster->toArray();
        $this->assertArrayHasKey('id', $createdCurrencyMaster);
        $this->assertNotNull($createdCurrencyMaster['id'], 'Created CurrencyMaster must have id specified');
        $this->assertNotNull(CurrencyMaster::find($createdCurrencyMaster['id']), 'CurrencyMaster with given id must be in DB');
        $this->assertModelData($currencyMaster, $createdCurrencyMaster);
    }

    /**
     * @test read
     */
    public function testReadCurrencyMaster()
    {
        $currencyMaster = $this->makeCurrencyMaster();
        $dbCurrencyMaster = $this->currencyMasterRepo->find($currencyMaster->id);
        $dbCurrencyMaster = $dbCurrencyMaster->toArray();
        $this->assertModelData($currencyMaster->toArray(), $dbCurrencyMaster);
    }

    /**
     * @test update
     */
    public function testUpdateCurrencyMaster()
    {
        $currencyMaster = $this->makeCurrencyMaster();
        $fakeCurrencyMaster = $this->fakeCurrencyMasterData();
        $updatedCurrencyMaster = $this->currencyMasterRepo->update($fakeCurrencyMaster, $currencyMaster->id);
        $this->assertModelData($fakeCurrencyMaster, $updatedCurrencyMaster->toArray());
        $dbCurrencyMaster = $this->currencyMasterRepo->find($currencyMaster->id);
        $this->assertModelData($fakeCurrencyMaster, $dbCurrencyMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCurrencyMaster()
    {
        $currencyMaster = $this->makeCurrencyMaster();
        $resp = $this->currencyMasterRepo->delete($currencyMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(CurrencyMaster::find($currencyMaster->id), 'CurrencyMaster should not exist in DB');
    }
}
