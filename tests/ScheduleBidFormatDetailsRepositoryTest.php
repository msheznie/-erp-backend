<?php namespace Tests\Repositories;

use App\Models\ScheduleBidFormatDetails;
use App\Repositories\ScheduleBidFormatDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ScheduleBidFormatDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ScheduleBidFormatDetailsRepository
     */
    protected $scheduleBidFormatDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->scheduleBidFormatDetailsRepo = \App::make(ScheduleBidFormatDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_schedule_bid_format_details()
    {
        $scheduleBidFormatDetails = factory(ScheduleBidFormatDetails::class)->make()->toArray();

        $createdScheduleBidFormatDetails = $this->scheduleBidFormatDetailsRepo->create($scheduleBidFormatDetails);

        $createdScheduleBidFormatDetails = $createdScheduleBidFormatDetails->toArray();
        $this->assertArrayHasKey('id', $createdScheduleBidFormatDetails);
        $this->assertNotNull($createdScheduleBidFormatDetails['id'], 'Created ScheduleBidFormatDetails must have id specified');
        $this->assertNotNull(ScheduleBidFormatDetails::find($createdScheduleBidFormatDetails['id']), 'ScheduleBidFormatDetails with given id must be in DB');
        $this->assertModelData($scheduleBidFormatDetails, $createdScheduleBidFormatDetails);
    }

    /**
     * @test read
     */
    public function test_read_schedule_bid_format_details()
    {
        $scheduleBidFormatDetails = factory(ScheduleBidFormatDetails::class)->create();

        $dbScheduleBidFormatDetails = $this->scheduleBidFormatDetailsRepo->find($scheduleBidFormatDetails->id);

        $dbScheduleBidFormatDetails = $dbScheduleBidFormatDetails->toArray();
        $this->assertModelData($scheduleBidFormatDetails->toArray(), $dbScheduleBidFormatDetails);
    }

    /**
     * @test update
     */
    public function test_update_schedule_bid_format_details()
    {
        $scheduleBidFormatDetails = factory(ScheduleBidFormatDetails::class)->create();
        $fakeScheduleBidFormatDetails = factory(ScheduleBidFormatDetails::class)->make()->toArray();

        $updatedScheduleBidFormatDetails = $this->scheduleBidFormatDetailsRepo->update($fakeScheduleBidFormatDetails, $scheduleBidFormatDetails->id);

        $this->assertModelData($fakeScheduleBidFormatDetails, $updatedScheduleBidFormatDetails->toArray());
        $dbScheduleBidFormatDetails = $this->scheduleBidFormatDetailsRepo->find($scheduleBidFormatDetails->id);
        $this->assertModelData($fakeScheduleBidFormatDetails, $dbScheduleBidFormatDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_schedule_bid_format_details()
    {
        $scheduleBidFormatDetails = factory(ScheduleBidFormatDetails::class)->create();

        $resp = $this->scheduleBidFormatDetailsRepo->delete($scheduleBidFormatDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(ScheduleBidFormatDetails::find($scheduleBidFormatDetails->id), 'ScheduleBidFormatDetails should not exist in DB');
    }
}
