<?php

use App\Models\DepreciationPeriodsReferredHistory;
use App\Repositories\DepreciationPeriodsReferredHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DepreciationPeriodsReferredHistoryRepositoryTest extends TestCase
{
    use MakeDepreciationPeriodsReferredHistoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DepreciationPeriodsReferredHistoryRepository
     */
    protected $depreciationPeriodsReferredHistoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->depreciationPeriodsReferredHistoryRepo = App::make(DepreciationPeriodsReferredHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDepreciationPeriodsReferredHistory()
    {
        $depreciationPeriodsReferredHistory = $this->fakeDepreciationPeriodsReferredHistoryData();
        $createdDepreciationPeriodsReferredHistory = $this->depreciationPeriodsReferredHistoryRepo->create($depreciationPeriodsReferredHistory);
        $createdDepreciationPeriodsReferredHistory = $createdDepreciationPeriodsReferredHistory->toArray();
        $this->assertArrayHasKey('id', $createdDepreciationPeriodsReferredHistory);
        $this->assertNotNull($createdDepreciationPeriodsReferredHistory['id'], 'Created DepreciationPeriodsReferredHistory must have id specified');
        $this->assertNotNull(DepreciationPeriodsReferredHistory::find($createdDepreciationPeriodsReferredHistory['id']), 'DepreciationPeriodsReferredHistory with given id must be in DB');
        $this->assertModelData($depreciationPeriodsReferredHistory, $createdDepreciationPeriodsReferredHistory);
    }

    /**
     * @test read
     */
    public function testReadDepreciationPeriodsReferredHistory()
    {
        $depreciationPeriodsReferredHistory = $this->makeDepreciationPeriodsReferredHistory();
        $dbDepreciationPeriodsReferredHistory = $this->depreciationPeriodsReferredHistoryRepo->find($depreciationPeriodsReferredHistory->id);
        $dbDepreciationPeriodsReferredHistory = $dbDepreciationPeriodsReferredHistory->toArray();
        $this->assertModelData($depreciationPeriodsReferredHistory->toArray(), $dbDepreciationPeriodsReferredHistory);
    }

    /**
     * @test update
     */
    public function testUpdateDepreciationPeriodsReferredHistory()
    {
        $depreciationPeriodsReferredHistory = $this->makeDepreciationPeriodsReferredHistory();
        $fakeDepreciationPeriodsReferredHistory = $this->fakeDepreciationPeriodsReferredHistoryData();
        $updatedDepreciationPeriodsReferredHistory = $this->depreciationPeriodsReferredHistoryRepo->update($fakeDepreciationPeriodsReferredHistory, $depreciationPeriodsReferredHistory->id);
        $this->assertModelData($fakeDepreciationPeriodsReferredHistory, $updatedDepreciationPeriodsReferredHistory->toArray());
        $dbDepreciationPeriodsReferredHistory = $this->depreciationPeriodsReferredHistoryRepo->find($depreciationPeriodsReferredHistory->id);
        $this->assertModelData($fakeDepreciationPeriodsReferredHistory, $dbDepreciationPeriodsReferredHistory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDepreciationPeriodsReferredHistory()
    {
        $depreciationPeriodsReferredHistory = $this->makeDepreciationPeriodsReferredHistory();
        $resp = $this->depreciationPeriodsReferredHistoryRepo->delete($depreciationPeriodsReferredHistory->id);
        $this->assertTrue($resp);
        $this->assertNull(DepreciationPeriodsReferredHistory::find($depreciationPeriodsReferredHistory->id), 'DepreciationPeriodsReferredHistory should not exist in DB');
    }
}
