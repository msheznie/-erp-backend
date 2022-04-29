<?php namespace Tests\Repositories;

use App\Models\ItemBatch;
use App\Repositories\ItemBatchRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ItemBatchRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemBatchRepository
     */
    protected $itemBatchRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->itemBatchRepo = \App::make(ItemBatchRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_item_batch()
    {
        $itemBatch = factory(ItemBatch::class)->make()->toArray();

        $createdItemBatch = $this->itemBatchRepo->create($itemBatch);

        $createdItemBatch = $createdItemBatch->toArray();
        $this->assertArrayHasKey('id', $createdItemBatch);
        $this->assertNotNull($createdItemBatch['id'], 'Created ItemBatch must have id specified');
        $this->assertNotNull(ItemBatch::find($createdItemBatch['id']), 'ItemBatch with given id must be in DB');
        $this->assertModelData($itemBatch, $createdItemBatch);
    }

    /**
     * @test read
     */
    public function test_read_item_batch()
    {
        $itemBatch = factory(ItemBatch::class)->create();

        $dbItemBatch = $this->itemBatchRepo->find($itemBatch->id);

        $dbItemBatch = $dbItemBatch->toArray();
        $this->assertModelData($itemBatch->toArray(), $dbItemBatch);
    }

    /**
     * @test update
     */
    public function test_update_item_batch()
    {
        $itemBatch = factory(ItemBatch::class)->create();
        $fakeItemBatch = factory(ItemBatch::class)->make()->toArray();

        $updatedItemBatch = $this->itemBatchRepo->update($fakeItemBatch, $itemBatch->id);

        $this->assertModelData($fakeItemBatch, $updatedItemBatch->toArray());
        $dbItemBatch = $this->itemBatchRepo->find($itemBatch->id);
        $this->assertModelData($fakeItemBatch, $dbItemBatch->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_item_batch()
    {
        $itemBatch = factory(ItemBatch::class)->create();

        $resp = $this->itemBatchRepo->delete($itemBatch->id);

        $this->assertTrue($resp);
        $this->assertNull(ItemBatch::find($itemBatch->id), 'ItemBatch should not exist in DB');
    }
}
