<?php namespace Tests\Repositories;

use App\Models\ItemCategoryTypeMasterTranslation;
use App\Repositories\ItemCategoryTypeMasterTranslationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ItemCategoryTypeMasterTranslationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemCategoryTypeMasterTranslationRepository
     */
    protected $itemCategoryTypeMasterTranslationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->itemCategoryTypeMasterTranslationRepo = \App::make(ItemCategoryTypeMasterTranslationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_item_category_type_master_translation()
    {
        $itemCategoryTypeMasterTranslation = factory(ItemCategoryTypeMasterTranslation::class)->make()->toArray();

        $createdItemCategoryTypeMasterTranslation = $this->itemCategoryTypeMasterTranslationRepo->create($itemCategoryTypeMasterTranslation);

        $createdItemCategoryTypeMasterTranslation = $createdItemCategoryTypeMasterTranslation->toArray();
        $this->assertArrayHasKey('id', $createdItemCategoryTypeMasterTranslation);
        $this->assertNotNull($createdItemCategoryTypeMasterTranslation['id'], 'Created ItemCategoryTypeMasterTranslation must have id specified');
        $this->assertNotNull(ItemCategoryTypeMasterTranslation::find($createdItemCategoryTypeMasterTranslation['id']), 'ItemCategoryTypeMasterTranslation with given id must be in DB');
        $this->assertModelData($itemCategoryTypeMasterTranslation, $createdItemCategoryTypeMasterTranslation);
    }

    /**
     * @test read
     */
    public function test_read_item_category_type_master_translation()
    {
        $itemCategoryTypeMasterTranslation = factory(ItemCategoryTypeMasterTranslation::class)->create();

        $dbItemCategoryTypeMasterTranslation = $this->itemCategoryTypeMasterTranslationRepo->find($itemCategoryTypeMasterTranslation->id);

        $dbItemCategoryTypeMasterTranslation = $dbItemCategoryTypeMasterTranslation->toArray();
        $this->assertModelData($itemCategoryTypeMasterTranslation->toArray(), $dbItemCategoryTypeMasterTranslation);
    }

    /**
     * @test update
     */
    public function test_update_item_category_type_master_translation()
    {
        $itemCategoryTypeMasterTranslation = factory(ItemCategoryTypeMasterTranslation::class)->create();
        $fakeItemCategoryTypeMasterTranslation = factory(ItemCategoryTypeMasterTranslation::class)->make()->toArray();

        $updatedItemCategoryTypeMasterTranslation = $this->itemCategoryTypeMasterTranslationRepo->update($fakeItemCategoryTypeMasterTranslation, $itemCategoryTypeMasterTranslation->id);

        $this->assertModelData($fakeItemCategoryTypeMasterTranslation, $updatedItemCategoryTypeMasterTranslation->toArray());
        $dbItemCategoryTypeMasterTranslation = $this->itemCategoryTypeMasterTranslationRepo->find($itemCategoryTypeMasterTranslation->id);
        $this->assertModelData($fakeItemCategoryTypeMasterTranslation, $dbItemCategoryTypeMasterTranslation->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_item_category_type_master_translation()
    {
        $itemCategoryTypeMasterTranslation = factory(ItemCategoryTypeMasterTranslation::class)->create();

        $resp = $this->itemCategoryTypeMasterTranslationRepo->delete($itemCategoryTypeMasterTranslation->id);

        $this->assertTrue($resp);
        $this->assertNull(ItemCategoryTypeMasterTranslation::find($itemCategoryTypeMasterTranslation->id), 'ItemCategoryTypeMasterTranslation should not exist in DB');
    }
}
