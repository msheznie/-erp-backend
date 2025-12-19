<?php

use App\Models\FixedAssetCategory;
use App\Repositories\FixedAssetCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FixedAssetCategoryRepositoryTest extends TestCase
{
    use MakeFixedAssetCategoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FixedAssetCategoryRepository
     */
    protected $fixedAssetCategoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->fixedAssetCategoryRepo = App::make(FixedAssetCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFixedAssetCategory()
    {
        $fixedAssetCategory = $this->fakeFixedAssetCategoryData();
        $createdFixedAssetCategory = $this->fixedAssetCategoryRepo->create($fixedAssetCategory);
        $createdFixedAssetCategory = $createdFixedAssetCategory->toArray();
        $this->assertArrayHasKey('id', $createdFixedAssetCategory);
        $this->assertNotNull($createdFixedAssetCategory['id'], 'Created FixedAssetCategory must have id specified');
        $this->assertNotNull(FixedAssetCategory::find($createdFixedAssetCategory['id']), 'FixedAssetCategory with given id must be in DB');
        $this->assertModelData($fixedAssetCategory, $createdFixedAssetCategory);
    }

    /**
     * @test read
     */
    public function testReadFixedAssetCategory()
    {
        $fixedAssetCategory = $this->makeFixedAssetCategory();
        $dbFixedAssetCategory = $this->fixedAssetCategoryRepo->find($fixedAssetCategory->id);
        $dbFixedAssetCategory = $dbFixedAssetCategory->toArray();
        $this->assertModelData($fixedAssetCategory->toArray(), $dbFixedAssetCategory);
    }

    /**
     * @test update
     */
    public function testUpdateFixedAssetCategory()
    {
        $fixedAssetCategory = $this->makeFixedAssetCategory();
        $fakeFixedAssetCategory = $this->fakeFixedAssetCategoryData();
        $updatedFixedAssetCategory = $this->fixedAssetCategoryRepo->update($fakeFixedAssetCategory, $fixedAssetCategory->id);
        $this->assertModelData($fakeFixedAssetCategory, $updatedFixedAssetCategory->toArray());
        $dbFixedAssetCategory = $this->fixedAssetCategoryRepo->find($fixedAssetCategory->id);
        $this->assertModelData($fakeFixedAssetCategory, $dbFixedAssetCategory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFixedAssetCategory()
    {
        $fixedAssetCategory = $this->makeFixedAssetCategory();
        $resp = $this->fixedAssetCategoryRepo->delete($fixedAssetCategory->id);
        $this->assertTrue($resp);
        $this->assertNull(FixedAssetCategory::find($fixedAssetCategory->id), 'FixedAssetCategory should not exist in DB');
    }
}
