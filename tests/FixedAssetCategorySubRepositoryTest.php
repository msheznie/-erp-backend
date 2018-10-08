<?php

use App\Models\FixedAssetCategorySub;
use App\Repositories\FixedAssetCategorySubRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FixedAssetCategorySubRepositoryTest extends TestCase
{
    use MakeFixedAssetCategorySubTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FixedAssetCategorySubRepository
     */
    protected $fixedAssetCategorySubRepo;

    public function setUp()
    {
        parent::setUp();
        $this->fixedAssetCategorySubRepo = App::make(FixedAssetCategorySubRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFixedAssetCategorySub()
    {
        $fixedAssetCategorySub = $this->fakeFixedAssetCategorySubData();
        $createdFixedAssetCategorySub = $this->fixedAssetCategorySubRepo->create($fixedAssetCategorySub);
        $createdFixedAssetCategorySub = $createdFixedAssetCategorySub->toArray();
        $this->assertArrayHasKey('id', $createdFixedAssetCategorySub);
        $this->assertNotNull($createdFixedAssetCategorySub['id'], 'Created FixedAssetCategorySub must have id specified');
        $this->assertNotNull(FixedAssetCategorySub::find($createdFixedAssetCategorySub['id']), 'FixedAssetCategorySub with given id must be in DB');
        $this->assertModelData($fixedAssetCategorySub, $createdFixedAssetCategorySub);
    }

    /**
     * @test read
     */
    public function testReadFixedAssetCategorySub()
    {
        $fixedAssetCategorySub = $this->makeFixedAssetCategorySub();
        $dbFixedAssetCategorySub = $this->fixedAssetCategorySubRepo->find($fixedAssetCategorySub->id);
        $dbFixedAssetCategorySub = $dbFixedAssetCategorySub->toArray();
        $this->assertModelData($fixedAssetCategorySub->toArray(), $dbFixedAssetCategorySub);
    }

    /**
     * @test update
     */
    public function testUpdateFixedAssetCategorySub()
    {
        $fixedAssetCategorySub = $this->makeFixedAssetCategorySub();
        $fakeFixedAssetCategorySub = $this->fakeFixedAssetCategorySubData();
        $updatedFixedAssetCategorySub = $this->fixedAssetCategorySubRepo->update($fakeFixedAssetCategorySub, $fixedAssetCategorySub->id);
        $this->assertModelData($fakeFixedAssetCategorySub, $updatedFixedAssetCategorySub->toArray());
        $dbFixedAssetCategorySub = $this->fixedAssetCategorySubRepo->find($fixedAssetCategorySub->id);
        $this->assertModelData($fakeFixedAssetCategorySub, $dbFixedAssetCategorySub->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFixedAssetCategorySub()
    {
        $fixedAssetCategorySub = $this->makeFixedAssetCategorySub();
        $resp = $this->fixedAssetCategorySubRepo->delete($fixedAssetCategorySub->id);
        $this->assertTrue($resp);
        $this->assertNull(FixedAssetCategorySub::find($fixedAssetCategorySub->id), 'FixedAssetCategorySub should not exist in DB');
    }
}
