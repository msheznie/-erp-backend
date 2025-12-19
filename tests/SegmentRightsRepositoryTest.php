<?php namespace Tests\Repositories;

use App\Models\SegmentRights;
use App\Repositories\SegmentRightsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeSegmentRightsTrait;
use Tests\ApiTestTrait;

class SegmentRightsRepositoryTest extends TestCase
{
    use MakeSegmentRightsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SegmentRightsRepository
     */
    protected $segmentRightsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->segmentRightsRepo = \App::make(SegmentRightsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_segment_rights()
    {
        $segmentRights = $this->fakeSegmentRightsData();
        $createdSegmentRights = $this->segmentRightsRepo->create($segmentRights);
        $createdSegmentRights = $createdSegmentRights->toArray();
        $this->assertArrayHasKey('id', $createdSegmentRights);
        $this->assertNotNull($createdSegmentRights['id'], 'Created SegmentRights must have id specified');
        $this->assertNotNull(SegmentRights::find($createdSegmentRights['id']), 'SegmentRights with given id must be in DB');
        $this->assertModelData($segmentRights, $createdSegmentRights);
    }

    /**
     * @test read
     */
    public function test_read_segment_rights()
    {
        $segmentRights = $this->makeSegmentRights();
        $dbSegmentRights = $this->segmentRightsRepo->find($segmentRights->id);
        $dbSegmentRights = $dbSegmentRights->toArray();
        $this->assertModelData($segmentRights->toArray(), $dbSegmentRights);
    }

    /**
     * @test update
     */
    public function test_update_segment_rights()
    {
        $segmentRights = $this->makeSegmentRights();
        $fakeSegmentRights = $this->fakeSegmentRightsData();
        $updatedSegmentRights = $this->segmentRightsRepo->update($fakeSegmentRights, $segmentRights->id);
        $this->assertModelData($fakeSegmentRights, $updatedSegmentRights->toArray());
        $dbSegmentRights = $this->segmentRightsRepo->find($segmentRights->id);
        $this->assertModelData($fakeSegmentRights, $dbSegmentRights->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_segment_rights()
    {
        $segmentRights = $this->makeSegmentRights();
        $resp = $this->segmentRightsRepo->delete($segmentRights->id);
        $this->assertTrue($resp);
        $this->assertNull(SegmentRights::find($segmentRights->id), 'SegmentRights should not exist in DB');
    }
}
