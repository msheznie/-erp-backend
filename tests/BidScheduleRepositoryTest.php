<?php namespace Tests\Repositories;

use App\Models\BidSchedule;
use App\Repositories\BidScheduleRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BidScheduleRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BidScheduleRepository
     */
    protected $bidScheduleRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->bidScheduleRepo = \App::make(BidScheduleRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_bid_schedule()
    {
        $bidSchedule = factory(BidSchedule::class)->make()->toArray();

        $createdBidSchedule = $this->bidScheduleRepo->create($bidSchedule);

        $createdBidSchedule = $createdBidSchedule->toArray();
        $this->assertArrayHasKey('id', $createdBidSchedule);
        $this->assertNotNull($createdBidSchedule['id'], 'Created BidSchedule must have id specified');
        $this->assertNotNull(BidSchedule::find($createdBidSchedule['id']), 'BidSchedule with given id must be in DB');
        $this->assertModelData($bidSchedule, $createdBidSchedule);
    }

    /**
     * @test read
     */
    public function test_read_bid_schedule()
    {
        $bidSchedule = factory(BidSchedule::class)->create();

        $dbBidSchedule = $this->bidScheduleRepo->find($bidSchedule->id);

        $dbBidSchedule = $dbBidSchedule->toArray();
        $this->assertModelData($bidSchedule->toArray(), $dbBidSchedule);
    }

    /**
     * @test update
     */
    public function test_update_bid_schedule()
    {
        $bidSchedule = factory(BidSchedule::class)->create();
        $fakeBidSchedule = factory(BidSchedule::class)->make()->toArray();

        $updatedBidSchedule = $this->bidScheduleRepo->update($fakeBidSchedule, $bidSchedule->id);

        $this->assertModelData($fakeBidSchedule, $updatedBidSchedule->toArray());
        $dbBidSchedule = $this->bidScheduleRepo->find($bidSchedule->id);
        $this->assertModelData($fakeBidSchedule, $dbBidSchedule->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_bid_schedule()
    {
        $bidSchedule = factory(BidSchedule::class)->create();

        $resp = $this->bidScheduleRepo->delete($bidSchedule->id);

        $this->assertTrue($resp);
        $this->assertNull(BidSchedule::find($bidSchedule->id), 'BidSchedule should not exist in DB');
    }
}
