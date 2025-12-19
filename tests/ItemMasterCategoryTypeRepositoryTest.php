<?php namespace Tests\Repositories;

use App\Models\ItemMasterCategoryType;
use App\Repositories\ItemMasterCategoryTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ItemMasterCategoryTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemMasterCategoryTypeRepository
     */
    protected $itemMasterCategoryTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->itemMasterCategoryTypeRepo = \App::make(ItemMasterCategoryTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_item_master_category_type()
    {
        $itemMasterCategoryType = factory(ItemMasterCategoryType::class)->make()->toArray();

        $createdItemMasterCategoryType = $this->itemMasterCategoryTypeRepo->create($itemMasterCategoryType);

        $createdItemMasterCategoryType = $createdItemMasterCategoryType->toArray();
        $this->assertArrayHasKey('id', $createdItemMasterCategoryType);
        $this->assertNotNull($createdItemMasterCategoryType['id'], 'Created ItemMasterCategoryType must have id specified');
        $this->assertNotNull(ItemMasterCategoryType::find($createdItemMasterCategoryType['id']), 'ItemMasterCategoryType with given id must be in DB');
        $this->assertModelData($itemMasterCategoryType, $createdItemMasterCategoryType);
    }

    /**
     * @test read
     */
    public function test_read_item_master_category_type()
    {
        $itemMasterCategoryType = factory(ItemMasterCategoryType::class)->create();

        $dbItemMasterCategoryType = $this->itemMasterCategoryTypeRepo->find($itemMasterCategoryType->id);

        $dbItemMasterCategoryType = $dbItemMasterCategoryType->toArray();
        $this->assertModelData($itemMasterCategoryType->toArray(), $dbItemMasterCategoryType);
    }

    /**
     * @test update
     */
    public function test_update_item_master_category_type()
    {
        $itemMasterCategoryType = factory(ItemMasterCategoryType::class)->create();
        $fakeItemMasterCategoryType = factory(ItemMasterCategoryType::class)->make()->toArray();

        $updatedItemMasterCategoryType = $this->itemMasterCategoryTypeRepo->update($fakeItemMasterCategoryType, $itemMasterCategoryType->id);

        $this->assertModelData($fakeItemMasterCategoryType, $updatedItemMasterCategoryType->toArray());
        $dbItemMasterCategoryType = $this->itemMasterCategoryTypeRepo->find($itemMasterCategoryType->id);
        $this->assertModelData($fakeItemMasterCategoryType, $dbItemMasterCategoryType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_item_master_category_type()
    {
        $itemMasterCategoryType = factory(ItemMasterCategoryType::class)->create();

        $resp = $this->itemMasterCategoryTypeRepo->delete($itemMasterCategoryType->id);

        $this->assertTrue($resp);
        $this->assertNull(ItemMasterCategoryType::find($itemMasterCategoryType->id), 'ItemMasterCategoryType should not exist in DB');
    }
}
