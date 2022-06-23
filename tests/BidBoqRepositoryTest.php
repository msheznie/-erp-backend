<?php namespace Tests\Repositories;

use App\Models\BidBoq;
use App\Repositories\BidBoqRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BidBoqRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BidBoqRepository
     */
    protected $bidBoqRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->bidBoqRepo = \App::make(BidBoqRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_bid_boq()
    {
        $bidBoq = factory(BidBoq::class)->make()->toArray();

        $createdBidBoq = $this->bidBoqRepo->create($bidBoq);

        $createdBidBoq = $createdBidBoq->toArray();
        $this->assertArrayHasKey('id', $createdBidBoq);
        $this->assertNotNull($createdBidBoq['id'], 'Created BidBoq must have id specified');
        $this->assertNotNull(BidBoq::find($createdBidBoq['id']), 'BidBoq with given id must be in DB');
        $this->assertModelData($bidBoq, $createdBidBoq);
    }

    /**
     * @test read
     */
    public function test_read_bid_boq()
    {
        $bidBoq = factory(BidBoq::class)->create();

        $dbBidBoq = $this->bidBoqRepo->find($bidBoq->id);

        $dbBidBoq = $dbBidBoq->toArray();
        $this->assertModelData($bidBoq->toArray(), $dbBidBoq);
    }

    /**
     * @test update
     */
    public function test_update_bid_boq()
    {
        $bidBoq = factory(BidBoq::class)->create();
        $fakeBidBoq = factory(BidBoq::class)->make()->toArray();

        $updatedBidBoq = $this->bidBoqRepo->update($fakeBidBoq, $bidBoq->id);

        $this->assertModelData($fakeBidBoq, $updatedBidBoq->toArray());
        $dbBidBoq = $this->bidBoqRepo->find($bidBoq->id);
        $this->assertModelData($fakeBidBoq, $dbBidBoq->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_bid_boq()
    {
        $bidBoq = factory(BidBoq::class)->create();

        $resp = $this->bidBoqRepo->delete($bidBoq->id);

        $this->assertTrue($resp);
        $this->assertNull(BidBoq::find($bidBoq->id), 'BidBoq should not exist in DB');
    }
}
