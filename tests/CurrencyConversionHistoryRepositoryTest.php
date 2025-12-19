<?php namespace Tests\Repositories;

use App\Models\CurrencyConversionHistory;
use App\Repositories\CurrencyConversionHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeCurrencyConversionHistoryTrait;
use Tests\ApiTestTrait;

class CurrencyConversionHistoryRepositoryTest extends TestCase
{
    use MakeCurrencyConversionHistoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CurrencyConversionHistoryRepository
     */
    protected $currencyConversionHistoryRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->currencyConversionHistoryRepo = \App::make(CurrencyConversionHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_currency_conversion_history()
    {
        $currencyConversionHistory = $this->fakeCurrencyConversionHistoryData();
        $createdCurrencyConversionHistory = $this->currencyConversionHistoryRepo->create($currencyConversionHistory);
        $createdCurrencyConversionHistory = $createdCurrencyConversionHistory->toArray();
        $this->assertArrayHasKey('id', $createdCurrencyConversionHistory);
        $this->assertNotNull($createdCurrencyConversionHistory['id'], 'Created CurrencyConversionHistory must have id specified');
        $this->assertNotNull(CurrencyConversionHistory::find($createdCurrencyConversionHistory['id']), 'CurrencyConversionHistory with given id must be in DB');
        $this->assertModelData($currencyConversionHistory, $createdCurrencyConversionHistory);
    }

    /**
     * @test read
     */
    public function test_read_currency_conversion_history()
    {
        $currencyConversionHistory = $this->makeCurrencyConversionHistory();
        $dbCurrencyConversionHistory = $this->currencyConversionHistoryRepo->find($currencyConversionHistory->id);
        $dbCurrencyConversionHistory = $dbCurrencyConversionHistory->toArray();
        $this->assertModelData($currencyConversionHistory->toArray(), $dbCurrencyConversionHistory);
    }

    /**
     * @test update
     */
    public function test_update_currency_conversion_history()
    {
        $currencyConversionHistory = $this->makeCurrencyConversionHistory();
        $fakeCurrencyConversionHistory = $this->fakeCurrencyConversionHistoryData();
        $updatedCurrencyConversionHistory = $this->currencyConversionHistoryRepo->update($fakeCurrencyConversionHistory, $currencyConversionHistory->id);
        $this->assertModelData($fakeCurrencyConversionHistory, $updatedCurrencyConversionHistory->toArray());
        $dbCurrencyConversionHistory = $this->currencyConversionHistoryRepo->find($currencyConversionHistory->id);
        $this->assertModelData($fakeCurrencyConversionHistory, $dbCurrencyConversionHistory->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_currency_conversion_history()
    {
        $currencyConversionHistory = $this->makeCurrencyConversionHistory();
        $resp = $this->currencyConversionHistoryRepo->delete($currencyConversionHistory->id);
        $this->assertTrue($resp);
        $this->assertNull(CurrencyConversionHistory::find($currencyConversionHistory->id), 'CurrencyConversionHistory should not exist in DB');
    }
}
