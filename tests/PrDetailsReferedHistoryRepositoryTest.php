<?php

use App\Models\PrDetailsReferedHistory;
use App\Repositories\PrDetailsReferedHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PrDetailsReferedHistoryRepositoryTest extends TestCase
{
    use MakePrDetailsReferedHistoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PrDetailsReferedHistoryRepository
     */
    protected $prDetailsReferedHistoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->prDetailsReferedHistoryRepo = App::make(PrDetailsReferedHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePrDetailsReferedHistory()
    {
        $prDetailsReferedHistory = $this->fakePrDetailsReferedHistoryData();
        $createdPrDetailsReferedHistory = $this->prDetailsReferedHistoryRepo->create($prDetailsReferedHistory);
        $createdPrDetailsReferedHistory = $createdPrDetailsReferedHistory->toArray();
        $this->assertArrayHasKey('id', $createdPrDetailsReferedHistory);
        $this->assertNotNull($createdPrDetailsReferedHistory['id'], 'Created PrDetailsReferedHistory must have id specified');
        $this->assertNotNull(PrDetailsReferedHistory::find($createdPrDetailsReferedHistory['id']), 'PrDetailsReferedHistory with given id must be in DB');
        $this->assertModelData($prDetailsReferedHistory, $createdPrDetailsReferedHistory);
    }

    /**
     * @test read
     */
    public function testReadPrDetailsReferedHistory()
    {
        $prDetailsReferedHistory = $this->makePrDetailsReferedHistory();
        $dbPrDetailsReferedHistory = $this->prDetailsReferedHistoryRepo->find($prDetailsReferedHistory->id);
        $dbPrDetailsReferedHistory = $dbPrDetailsReferedHistory->toArray();
        $this->assertModelData($prDetailsReferedHistory->toArray(), $dbPrDetailsReferedHistory);
    }

    /**
     * @test update
     */
    public function testUpdatePrDetailsReferedHistory()
    {
        $prDetailsReferedHistory = $this->makePrDetailsReferedHistory();
        $fakePrDetailsReferedHistory = $this->fakePrDetailsReferedHistoryData();
        $updatedPrDetailsReferedHistory = $this->prDetailsReferedHistoryRepo->update($fakePrDetailsReferedHistory, $prDetailsReferedHistory->id);
        $this->assertModelData($fakePrDetailsReferedHistory, $updatedPrDetailsReferedHistory->toArray());
        $dbPrDetailsReferedHistory = $this->prDetailsReferedHistoryRepo->find($prDetailsReferedHistory->id);
        $this->assertModelData($fakePrDetailsReferedHistory, $dbPrDetailsReferedHistory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePrDetailsReferedHistory()
    {
        $prDetailsReferedHistory = $this->makePrDetailsReferedHistory();
        $resp = $this->prDetailsReferedHistoryRepo->delete($prDetailsReferedHistory->id);
        $this->assertTrue($resp);
        $this->assertNull(PrDetailsReferedHistory::find($prDetailsReferedHistory->id), 'PrDetailsReferedHistory should not exist in DB');
    }
}
