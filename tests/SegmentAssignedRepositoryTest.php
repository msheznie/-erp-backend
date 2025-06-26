<?php namespace Tests\Repositories;

use App\Models\SegmentAssigned;
use App\Repositories\SegmentAssignedRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SegmentAssignedRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SegmentAssignedRepository
     */
    protected $segmentAssignedRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->segmentAssignedRepo = \App::make(SegmentAssignedRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_segment_assigned()
    {
        $segmentAssigned = factory(SegmentAssigned::class)->make()->toArray();

        $createdSegmentAssigned = $this->segmentAssignedRepo->create($segmentAssigned);

        $createdSegmentAssigned = $createdSegmentAssigned->toArray();
        $this->assertArrayHasKey('id', $createdSegmentAssigned);
        $this->assertNotNull($createdSegmentAssigned['id'], 'Created SegmentAssigned must have id specified');
        $this->assertNotNull(SegmentAssigned::find($createdSegmentAssigned['id']), 'SegmentAssigned with given id must be in DB');
        $this->assertModelData($segmentAssigned, $createdSegmentAssigned);
    }

    /**
     * @test read
     */
    public function test_read_segment_assigned()
    {
        $segmentAssigned = factory(SegmentAssigned::class)->create();

        $dbSegmentAssigned = $this->segmentAssignedRepo->find($segmentAssigned->id);

        $dbSegmentAssigned = $dbSegmentAssigned->toArray();
        $this->assertModelData($segmentAssigned->toArray(), $dbSegmentAssigned);
    }

    /**
     * @test update
     */
    public function test_update_segment_assigned()
    {
        $segmentAssigned = factory(SegmentAssigned::class)->create();
        $fakeSegmentAssigned = factory(SegmentAssigned::class)->make()->toArray();

        $updatedSegmentAssigned = $this->segmentAssignedRepo->update($fakeSegmentAssigned, $segmentAssigned->id);

        $this->assertModelData($fakeSegmentAssigned, $updatedSegmentAssigned->toArray());
        $dbSegmentAssigned = $this->segmentAssignedRepo->find($segmentAssigned->id);
        $this->assertModelData($fakeSegmentAssigned, $dbSegmentAssigned->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_segment_assigned()
    {
        $segmentAssigned = factory(SegmentAssigned::class)->create();

        $resp = $this->segmentAssignedRepo->delete($segmentAssigned->id);

        $this->assertTrue($resp);
        $this->assertNull(SegmentAssigned::find($segmentAssigned->id), 'SegmentAssigned should not exist in DB');
    }
}
