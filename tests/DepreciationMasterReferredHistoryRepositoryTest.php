<?php

use App\Models\DepreciationMasterReferredHistory;
use App\Repositories\DepreciationMasterReferredHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DepreciationMasterReferredHistoryRepositoryTest extends TestCase
{
    use MakeDepreciationMasterReferredHistoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DepreciationMasterReferredHistoryRepository
     */
    protected $depreciationMasterReferredHistoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->depreciationMasterReferredHistoryRepo = App::make(DepreciationMasterReferredHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDepreciationMasterReferredHistory()
    {
        $depreciationMasterReferredHistory = $this->fakeDepreciationMasterReferredHistoryData();
        $createdDepreciationMasterReferredHistory = $this->depreciationMasterReferredHistoryRepo->create($depreciationMasterReferredHistory);
        $createdDepreciationMasterReferredHistory = $createdDepreciationMasterReferredHistory->toArray();
        $this->assertArrayHasKey('id', $createdDepreciationMasterReferredHistory);
        $this->assertNotNull($createdDepreciationMasterReferredHistory['id'], 'Created DepreciationMasterReferredHistory must have id specified');
        $this->assertNotNull(DepreciationMasterReferredHistory::find($createdDepreciationMasterReferredHistory['id']), 'DepreciationMasterReferredHistory with given id must be in DB');
        $this->assertModelData($depreciationMasterReferredHistory, $createdDepreciationMasterReferredHistory);
    }

    /**
     * @test read
     */
    public function testReadDepreciationMasterReferredHistory()
    {
        $depreciationMasterReferredHistory = $this->makeDepreciationMasterReferredHistory();
        $dbDepreciationMasterReferredHistory = $this->depreciationMasterReferredHistoryRepo->find($depreciationMasterReferredHistory->id);
        $dbDepreciationMasterReferredHistory = $dbDepreciationMasterReferredHistory->toArray();
        $this->assertModelData($depreciationMasterReferredHistory->toArray(), $dbDepreciationMasterReferredHistory);
    }

    /**
     * @test update
     */
    public function testUpdateDepreciationMasterReferredHistory()
    {
        $depreciationMasterReferredHistory = $this->makeDepreciationMasterReferredHistory();
        $fakeDepreciationMasterReferredHistory = $this->fakeDepreciationMasterReferredHistoryData();
        $updatedDepreciationMasterReferredHistory = $this->depreciationMasterReferredHistoryRepo->update($fakeDepreciationMasterReferredHistory, $depreciationMasterReferredHistory->id);
        $this->assertModelData($fakeDepreciationMasterReferredHistory, $updatedDepreciationMasterReferredHistory->toArray());
        $dbDepreciationMasterReferredHistory = $this->depreciationMasterReferredHistoryRepo->find($depreciationMasterReferredHistory->id);
        $this->assertModelData($fakeDepreciationMasterReferredHistory, $dbDepreciationMasterReferredHistory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDepreciationMasterReferredHistory()
    {
        $depreciationMasterReferredHistory = $this->makeDepreciationMasterReferredHistory();
        $resp = $this->depreciationMasterReferredHistoryRepo->delete($depreciationMasterReferredHistory->id);
        $this->assertTrue($resp);
        $this->assertNull(DepreciationMasterReferredHistory::find($depreciationMasterReferredHistory->id), 'DepreciationMasterReferredHistory should not exist in DB');
    }
}
