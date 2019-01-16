<?php

use App\Models\CurrencyDenomination;
use App\Repositories\CurrencyDenominationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CurrencyDenominationRepositoryTest extends TestCase
{
    use MakeCurrencyDenominationTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CurrencyDenominationRepository
     */
    protected $currencyDenominationRepo;

    public function setUp()
    {
        parent::setUp();
        $this->currencyDenominationRepo = App::make(CurrencyDenominationRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCurrencyDenomination()
    {
        $currencyDenomination = $this->fakeCurrencyDenominationData();
        $createdCurrencyDenomination = $this->currencyDenominationRepo->create($currencyDenomination);
        $createdCurrencyDenomination = $createdCurrencyDenomination->toArray();
        $this->assertArrayHasKey('id', $createdCurrencyDenomination);
        $this->assertNotNull($createdCurrencyDenomination['id'], 'Created CurrencyDenomination must have id specified');
        $this->assertNotNull(CurrencyDenomination::find($createdCurrencyDenomination['id']), 'CurrencyDenomination with given id must be in DB');
        $this->assertModelData($currencyDenomination, $createdCurrencyDenomination);
    }

    /**
     * @test read
     */
    public function testReadCurrencyDenomination()
    {
        $currencyDenomination = $this->makeCurrencyDenomination();
        $dbCurrencyDenomination = $this->currencyDenominationRepo->find($currencyDenomination->id);
        $dbCurrencyDenomination = $dbCurrencyDenomination->toArray();
        $this->assertModelData($currencyDenomination->toArray(), $dbCurrencyDenomination);
    }

    /**
     * @test update
     */
    public function testUpdateCurrencyDenomination()
    {
        $currencyDenomination = $this->makeCurrencyDenomination();
        $fakeCurrencyDenomination = $this->fakeCurrencyDenominationData();
        $updatedCurrencyDenomination = $this->currencyDenominationRepo->update($fakeCurrencyDenomination, $currencyDenomination->id);
        $this->assertModelData($fakeCurrencyDenomination, $updatedCurrencyDenomination->toArray());
        $dbCurrencyDenomination = $this->currencyDenominationRepo->find($currencyDenomination->id);
        $this->assertModelData($fakeCurrencyDenomination, $dbCurrencyDenomination->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCurrencyDenomination()
    {
        $currencyDenomination = $this->makeCurrencyDenomination();
        $resp = $this->currencyDenominationRepo->delete($currencyDenomination->id);
        $this->assertTrue($resp);
        $this->assertNull(CurrencyDenomination::find($currencyDenomination->id), 'CurrencyDenomination should not exist in DB');
    }
}
