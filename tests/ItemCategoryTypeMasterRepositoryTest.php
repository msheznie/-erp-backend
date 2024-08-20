<?php namespace Tests\Repositories;

use App\Models\ItemCategoryTypeMaster;
use App\Repositories\ItemCategoryTypeMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ItemCategoryTypeMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemCategoryTypeMasterRepository
     */
    protected $itemCategoryTypeMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->itemCategoryTypeMasterRepo = \App::make(ItemCategoryTypeMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_item_category_type_master()
    {
        $itemCategoryTypeMaster = factory(ItemCategoryTypeMaster::class)->make()->toArray();

        $createdItemCategoryTypeMaster = $this->itemCategoryTypeMasterRepo->create($itemCategoryTypeMaster);

        $createdItemCategoryTypeMaster = $createdItemCategoryTypeMaster->toArray();
        $this->assertArrayHasKey('id', $createdItemCategoryTypeMaster);
        $this->assertNotNull($createdItemCategoryTypeMaster['id'], 'Created ItemCategoryTypeMaster must have id specified');
        $this->assertNotNull(ItemCategoryTypeMaster::find($createdItemCategoryTypeMaster['id']), 'ItemCategoryTypeMaster with given id must be in DB');
        $this->assertModelData($itemCategoryTypeMaster, $createdItemCategoryTypeMaster);
    }

    /**
     * @test read
     */
    public function test_read_item_category_type_master()
    {
        $itemCategoryTypeMaster = factory(ItemCategoryTypeMaster::class)->create();

        $dbItemCategoryTypeMaster = $this->itemCategoryTypeMasterRepo->find($itemCategoryTypeMaster->id);

        $dbItemCategoryTypeMaster = $dbItemCategoryTypeMaster->toArray();
        $this->assertModelData($itemCategoryTypeMaster->toArray(), $dbItemCategoryTypeMaster);
    }

    /**
     * @test update
     */
    public function test_update_item_category_type_master()
    {
        $itemCategoryTypeMaster = factory(ItemCategoryTypeMaster::class)->create();
        $fakeItemCategoryTypeMaster = factory(ItemCategoryTypeMaster::class)->make()->toArray();

        $updatedItemCategoryTypeMaster = $this->itemCategoryTypeMasterRepo->update($fakeItemCategoryTypeMaster, $itemCategoryTypeMaster->id);

        $this->assertModelData($fakeItemCategoryTypeMaster, $updatedItemCategoryTypeMaster->toArray());
        $dbItemCategoryTypeMaster = $this->itemCategoryTypeMasterRepo->find($itemCategoryTypeMaster->id);
        $this->assertModelData($fakeItemCategoryTypeMaster, $dbItemCategoryTypeMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_item_category_type_master()
    {
        $itemCategoryTypeMaster = factory(ItemCategoryTypeMaster::class)->create();

        $resp = $this->itemCategoryTypeMasterRepo->delete($itemCategoryTypeMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(ItemCategoryTypeMaster::find($itemCategoryTypeMaster->id), 'ItemCategoryTypeMaster should not exist in DB');
    }
}
