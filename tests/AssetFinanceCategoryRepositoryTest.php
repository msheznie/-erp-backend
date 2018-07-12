<?php

use App\Models\AssetFinanceCategory;
use App\Repositories\AssetFinanceCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetFinanceCategoryRepositoryTest extends TestCase
{
    use MakeAssetFinanceCategoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetFinanceCategoryRepository
     */
    protected $assetFinanceCategoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->assetFinanceCategoryRepo = App::make(AssetFinanceCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAssetFinanceCategory()
    {
        $assetFinanceCategory = $this->fakeAssetFinanceCategoryData();
        $createdAssetFinanceCategory = $this->assetFinanceCategoryRepo->create($assetFinanceCategory);
        $createdAssetFinanceCategory = $createdAssetFinanceCategory->toArray();
        $this->assertArrayHasKey('id', $createdAssetFinanceCategory);
        $this->assertNotNull($createdAssetFinanceCategory['id'], 'Created AssetFinanceCategory must have id specified');
        $this->assertNotNull(AssetFinanceCategory::find($createdAssetFinanceCategory['id']), 'AssetFinanceCategory with given id must be in DB');
        $this->assertModelData($assetFinanceCategory, $createdAssetFinanceCategory);
    }

    /**
     * @test read
     */
    public function testReadAssetFinanceCategory()
    {
        $assetFinanceCategory = $this->makeAssetFinanceCategory();
        $dbAssetFinanceCategory = $this->assetFinanceCategoryRepo->find($assetFinanceCategory->id);
        $dbAssetFinanceCategory = $dbAssetFinanceCategory->toArray();
        $this->assertModelData($assetFinanceCategory->toArray(), $dbAssetFinanceCategory);
    }

    /**
     * @test update
     */
    public function testUpdateAssetFinanceCategory()
    {
        $assetFinanceCategory = $this->makeAssetFinanceCategory();
        $fakeAssetFinanceCategory = $this->fakeAssetFinanceCategoryData();
        $updatedAssetFinanceCategory = $this->assetFinanceCategoryRepo->update($fakeAssetFinanceCategory, $assetFinanceCategory->id);
        $this->assertModelData($fakeAssetFinanceCategory, $updatedAssetFinanceCategory->toArray());
        $dbAssetFinanceCategory = $this->assetFinanceCategoryRepo->find($assetFinanceCategory->id);
        $this->assertModelData($fakeAssetFinanceCategory, $dbAssetFinanceCategory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAssetFinanceCategory()
    {
        $assetFinanceCategory = $this->makeAssetFinanceCategory();
        $resp = $this->assetFinanceCategoryRepo->delete($assetFinanceCategory->id);
        $this->assertTrue($resp);
        $this->assertNull(AssetFinanceCategory::find($assetFinanceCategory->id), 'AssetFinanceCategory should not exist in DB');
    }
}
