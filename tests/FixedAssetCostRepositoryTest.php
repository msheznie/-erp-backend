<?php

use App\Models\FixedAssetCost;
use App\Repositories\FixedAssetCostRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FixedAssetCostRepositoryTest extends TestCase
{
    use MakeFixedAssetCostTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FixedAssetCostRepository
     */
    protected $fixedAssetCostRepo;

    public function setUp()
    {
        parent::setUp();
        $this->fixedAssetCostRepo = App::make(FixedAssetCostRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFixedAssetCost()
    {
        $fixedAssetCost = $this->fakeFixedAssetCostData();
        $createdFixedAssetCost = $this->fixedAssetCostRepo->create($fixedAssetCost);
        $createdFixedAssetCost = $createdFixedAssetCost->toArray();
        $this->assertArrayHasKey('id', $createdFixedAssetCost);
        $this->assertNotNull($createdFixedAssetCost['id'], 'Created FixedAssetCost must have id specified');
        $this->assertNotNull(FixedAssetCost::find($createdFixedAssetCost['id']), 'FixedAssetCost with given id must be in DB');
        $this->assertModelData($fixedAssetCost, $createdFixedAssetCost);
    }

    /**
     * @test read
     */
    public function testReadFixedAssetCost()
    {
        $fixedAssetCost = $this->makeFixedAssetCost();
        $dbFixedAssetCost = $this->fixedAssetCostRepo->find($fixedAssetCost->id);
        $dbFixedAssetCost = $dbFixedAssetCost->toArray();
        $this->assertModelData($fixedAssetCost->toArray(), $dbFixedAssetCost);
    }

    /**
     * @test update
     */
    public function testUpdateFixedAssetCost()
    {
        $fixedAssetCost = $this->makeFixedAssetCost();
        $fakeFixedAssetCost = $this->fakeFixedAssetCostData();
        $updatedFixedAssetCost = $this->fixedAssetCostRepo->update($fakeFixedAssetCost, $fixedAssetCost->id);
        $this->assertModelData($fakeFixedAssetCost, $updatedFixedAssetCost->toArray());
        $dbFixedAssetCost = $this->fixedAssetCostRepo->find($fixedAssetCost->id);
        $this->assertModelData($fakeFixedAssetCost, $dbFixedAssetCost->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFixedAssetCost()
    {
        $fixedAssetCost = $this->makeFixedAssetCost();
        $resp = $this->fixedAssetCostRepo->delete($fixedAssetCost->id);
        $this->assertTrue($resp);
        $this->assertNull(FixedAssetCost::find($fixedAssetCost->id), 'FixedAssetCost should not exist in DB');
    }
}
