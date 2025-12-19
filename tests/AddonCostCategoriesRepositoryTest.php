<?php

use App\Models\AddonCostCategories;
use App\Repositories\AddonCostCategoriesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AddonCostCategoriesRepositoryTest extends TestCase
{
    use MakeAddonCostCategoriesTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AddonCostCategoriesRepository
     */
    protected $addonCostCategoriesRepo;

    public function setUp()
    {
        parent::setUp();
        $this->addonCostCategoriesRepo = App::make(AddonCostCategoriesRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAddonCostCategories()
    {
        $addonCostCategories = $this->fakeAddonCostCategoriesData();
        $createdAddonCostCategories = $this->addonCostCategoriesRepo->create($addonCostCategories);
        $createdAddonCostCategories = $createdAddonCostCategories->toArray();
        $this->assertArrayHasKey('id', $createdAddonCostCategories);
        $this->assertNotNull($createdAddonCostCategories['id'], 'Created AddonCostCategories must have id specified');
        $this->assertNotNull(AddonCostCategories::find($createdAddonCostCategories['id']), 'AddonCostCategories with given id must be in DB');
        $this->assertModelData($addonCostCategories, $createdAddonCostCategories);
    }

    /**
     * @test read
     */
    public function testReadAddonCostCategories()
    {
        $addonCostCategories = $this->makeAddonCostCategories();
        $dbAddonCostCategories = $this->addonCostCategoriesRepo->find($addonCostCategories->id);
        $dbAddonCostCategories = $dbAddonCostCategories->toArray();
        $this->assertModelData($addonCostCategories->toArray(), $dbAddonCostCategories);
    }

    /**
     * @test update
     */
    public function testUpdateAddonCostCategories()
    {
        $addonCostCategories = $this->makeAddonCostCategories();
        $fakeAddonCostCategories = $this->fakeAddonCostCategoriesData();
        $updatedAddonCostCategories = $this->addonCostCategoriesRepo->update($fakeAddonCostCategories, $addonCostCategories->id);
        $this->assertModelData($fakeAddonCostCategories, $updatedAddonCostCategories->toArray());
        $dbAddonCostCategories = $this->addonCostCategoriesRepo->find($addonCostCategories->id);
        $this->assertModelData($fakeAddonCostCategories, $dbAddonCostCategories->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAddonCostCategories()
    {
        $addonCostCategories = $this->makeAddonCostCategories();
        $resp = $this->addonCostCategoriesRepo->delete($addonCostCategories->id);
        $this->assertTrue($resp);
        $this->assertNull(AddonCostCategories::find($addonCostCategories->id), 'AddonCostCategories should not exist in DB');
    }
}
