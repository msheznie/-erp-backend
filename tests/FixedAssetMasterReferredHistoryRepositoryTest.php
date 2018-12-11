<?php

use App\Models\FixedAssetMasterReferredHistory;
use App\Repositories\FixedAssetMasterReferredHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FixedAssetMasterReferredHistoryRepositoryTest extends TestCase
{
    use MakeFixedAssetMasterReferredHistoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FixedAssetMasterReferredHistoryRepository
     */
    protected $fixedAssetMasterReferredHistoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->fixedAssetMasterReferredHistoryRepo = App::make(FixedAssetMasterReferredHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFixedAssetMasterReferredHistory()
    {
        $fixedAssetMasterReferredHistory = $this->fakeFixedAssetMasterReferredHistoryData();
        $createdFixedAssetMasterReferredHistory = $this->fixedAssetMasterReferredHistoryRepo->create($fixedAssetMasterReferredHistory);
        $createdFixedAssetMasterReferredHistory = $createdFixedAssetMasterReferredHistory->toArray();
        $this->assertArrayHasKey('id', $createdFixedAssetMasterReferredHistory);
        $this->assertNotNull($createdFixedAssetMasterReferredHistory['id'], 'Created FixedAssetMasterReferredHistory must have id specified');
        $this->assertNotNull(FixedAssetMasterReferredHistory::find($createdFixedAssetMasterReferredHistory['id']), 'FixedAssetMasterReferredHistory with given id must be in DB');
        $this->assertModelData($fixedAssetMasterReferredHistory, $createdFixedAssetMasterReferredHistory);
    }

    /**
     * @test read
     */
    public function testReadFixedAssetMasterReferredHistory()
    {
        $fixedAssetMasterReferredHistory = $this->makeFixedAssetMasterReferredHistory();
        $dbFixedAssetMasterReferredHistory = $this->fixedAssetMasterReferredHistoryRepo->find($fixedAssetMasterReferredHistory->id);
        $dbFixedAssetMasterReferredHistory = $dbFixedAssetMasterReferredHistory->toArray();
        $this->assertModelData($fixedAssetMasterReferredHistory->toArray(), $dbFixedAssetMasterReferredHistory);
    }

    /**
     * @test update
     */
    public function testUpdateFixedAssetMasterReferredHistory()
    {
        $fixedAssetMasterReferredHistory = $this->makeFixedAssetMasterReferredHistory();
        $fakeFixedAssetMasterReferredHistory = $this->fakeFixedAssetMasterReferredHistoryData();
        $updatedFixedAssetMasterReferredHistory = $this->fixedAssetMasterReferredHistoryRepo->update($fakeFixedAssetMasterReferredHistory, $fixedAssetMasterReferredHistory->id);
        $this->assertModelData($fakeFixedAssetMasterReferredHistory, $updatedFixedAssetMasterReferredHistory->toArray());
        $dbFixedAssetMasterReferredHistory = $this->fixedAssetMasterReferredHistoryRepo->find($fixedAssetMasterReferredHistory->id);
        $this->assertModelData($fakeFixedAssetMasterReferredHistory, $dbFixedAssetMasterReferredHistory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFixedAssetMasterReferredHistory()
    {
        $fixedAssetMasterReferredHistory = $this->makeFixedAssetMasterReferredHistory();
        $resp = $this->fixedAssetMasterReferredHistoryRepo->delete($fixedAssetMasterReferredHistory->id);
        $this->assertTrue($resp);
        $this->assertNull(FixedAssetMasterReferredHistory::find($fixedAssetMasterReferredHistory->id), 'FixedAssetMasterReferredHistory should not exist in DB');
    }
}
