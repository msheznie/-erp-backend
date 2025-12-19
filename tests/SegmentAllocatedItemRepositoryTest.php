<?php namespace Tests\Repositories;

use App\Models\SegmentAllocatedItem;
use App\Repositories\SegmentAllocatedItemRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SegmentAllocatedItemRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SegmentAllocatedItemRepository
     */
    protected $segmentAllocatedItemRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->segmentAllocatedItemRepo = \App::make(SegmentAllocatedItemRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_segment_allocated_item()
    {
        $segmentAllocatedItem = factory(SegmentAllocatedItem::class)->make()->toArray();

        $createdSegmentAllocatedItem = $this->segmentAllocatedItemRepo->create($segmentAllocatedItem);

        $createdSegmentAllocatedItem = $createdSegmentAllocatedItem->toArray();
        $this->assertArrayHasKey('id', $createdSegmentAllocatedItem);
        $this->assertNotNull($createdSegmentAllocatedItem['id'], 'Created SegmentAllocatedItem must have id specified');
        $this->assertNotNull(SegmentAllocatedItem::find($createdSegmentAllocatedItem['id']), 'SegmentAllocatedItem with given id must be in DB');
        $this->assertModelData($segmentAllocatedItem, $createdSegmentAllocatedItem);
    }

    /**
     * @test read
     */
    public function test_read_segment_allocated_item()
    {
        $segmentAllocatedItem = factory(SegmentAllocatedItem::class)->create();

        $dbSegmentAllocatedItem = $this->segmentAllocatedItemRepo->find($segmentAllocatedItem->id);

        $dbSegmentAllocatedItem = $dbSegmentAllocatedItem->toArray();
        $this->assertModelData($segmentAllocatedItem->toArray(), $dbSegmentAllocatedItem);
    }

    /**
     * @test update
     */
    public function test_update_segment_allocated_item()
    {
        $segmentAllocatedItem = factory(SegmentAllocatedItem::class)->create();
        $fakeSegmentAllocatedItem = factory(SegmentAllocatedItem::class)->make()->toArray();

        $updatedSegmentAllocatedItem = $this->segmentAllocatedItemRepo->update($fakeSegmentAllocatedItem, $segmentAllocatedItem->id);

        $this->assertModelData($fakeSegmentAllocatedItem, $updatedSegmentAllocatedItem->toArray());
        $dbSegmentAllocatedItem = $this->segmentAllocatedItemRepo->find($segmentAllocatedItem->id);
        $this->assertModelData($fakeSegmentAllocatedItem, $dbSegmentAllocatedItem->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_segment_allocated_item()
    {
        $segmentAllocatedItem = factory(SegmentAllocatedItem::class)->create();

        $resp = $this->segmentAllocatedItemRepo->delete($segmentAllocatedItem->id);

        $this->assertTrue($resp);
        $this->assertNull(SegmentAllocatedItem::find($segmentAllocatedItem->id), 'SegmentAllocatedItem should not exist in DB');
    }
}
