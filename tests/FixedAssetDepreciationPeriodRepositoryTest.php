<?php

use App\Models\FixedAssetDepreciationPeriod;
use App\Repositories\FixedAssetDepreciationPeriodRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FixedAssetDepreciationPeriodRepositoryTest extends TestCase
{
    use MakeFixedAssetDepreciationPeriodTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FixedAssetDepreciationPeriodRepository
     */
    protected $fixedAssetDepreciationPeriodRepo;

    public function setUp()
    {
        parent::setUp();
        $this->fixedAssetDepreciationPeriodRepo = App::make(FixedAssetDepreciationPeriodRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFixedAssetDepreciationPeriod()
    {
        $fixedAssetDepreciationPeriod = $this->fakeFixedAssetDepreciationPeriodData();
        $createdFixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepo->create($fixedAssetDepreciationPeriod);
        $createdFixedAssetDepreciationPeriod = $createdFixedAssetDepreciationPeriod->toArray();
        $this->assertArrayHasKey('id', $createdFixedAssetDepreciationPeriod);
        $this->assertNotNull($createdFixedAssetDepreciationPeriod['id'], 'Created FixedAssetDepreciationPeriod must have id specified');
        $this->assertNotNull(FixedAssetDepreciationPeriod::find($createdFixedAssetDepreciationPeriod['id']), 'FixedAssetDepreciationPeriod with given id must be in DB');
        $this->assertModelData($fixedAssetDepreciationPeriod, $createdFixedAssetDepreciationPeriod);
    }

    /**
     * @test read
     */
    public function testReadFixedAssetDepreciationPeriod()
    {
        $fixedAssetDepreciationPeriod = $this->makeFixedAssetDepreciationPeriod();
        $dbFixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepo->find($fixedAssetDepreciationPeriod->id);
        $dbFixedAssetDepreciationPeriod = $dbFixedAssetDepreciationPeriod->toArray();
        $this->assertModelData($fixedAssetDepreciationPeriod->toArray(), $dbFixedAssetDepreciationPeriod);
    }

    /**
     * @test update
     */
    public function testUpdateFixedAssetDepreciationPeriod()
    {
        $fixedAssetDepreciationPeriod = $this->makeFixedAssetDepreciationPeriod();
        $fakeFixedAssetDepreciationPeriod = $this->fakeFixedAssetDepreciationPeriodData();
        $updatedFixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepo->update($fakeFixedAssetDepreciationPeriod, $fixedAssetDepreciationPeriod->id);
        $this->assertModelData($fakeFixedAssetDepreciationPeriod, $updatedFixedAssetDepreciationPeriod->toArray());
        $dbFixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepo->find($fixedAssetDepreciationPeriod->id);
        $this->assertModelData($fakeFixedAssetDepreciationPeriod, $dbFixedAssetDepreciationPeriod->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFixedAssetDepreciationPeriod()
    {
        $fixedAssetDepreciationPeriod = $this->makeFixedAssetDepreciationPeriod();
        $resp = $this->fixedAssetDepreciationPeriodRepo->delete($fixedAssetDepreciationPeriod->id);
        $this->assertTrue($resp);
        $this->assertNull(FixedAssetDepreciationPeriod::find($fixedAssetDepreciationPeriod->id), 'FixedAssetDepreciationPeriod should not exist in DB');
    }
}
