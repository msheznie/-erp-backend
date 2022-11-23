<?php namespace Tests\Repositories;

use App\Models\BidSubmissionDetail;
use App\Repositories\BidSubmissionDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BidSubmissionDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BidSubmissionDetailRepository
     */
    protected $bidSubmissionDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->bidSubmissionDetailRepo = \App::make(BidSubmissionDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_bid_submission_detail()
    {
        $bidSubmissionDetail = factory(BidSubmissionDetail::class)->make()->toArray();

        $createdBidSubmissionDetail = $this->bidSubmissionDetailRepo->create($bidSubmissionDetail);

        $createdBidSubmissionDetail = $createdBidSubmissionDetail->toArray();
        $this->assertArrayHasKey('id', $createdBidSubmissionDetail);
        $this->assertNotNull($createdBidSubmissionDetail['id'], 'Created BidSubmissionDetail must have id specified');
        $this->assertNotNull(BidSubmissionDetail::find($createdBidSubmissionDetail['id']), 'BidSubmissionDetail with given id must be in DB');
        $this->assertModelData($bidSubmissionDetail, $createdBidSubmissionDetail);
    }

    /**
     * @test read
     */
    public function test_read_bid_submission_detail()
    {
        $bidSubmissionDetail = factory(BidSubmissionDetail::class)->create();

        $dbBidSubmissionDetail = $this->bidSubmissionDetailRepo->find($bidSubmissionDetail->id);

        $dbBidSubmissionDetail = $dbBidSubmissionDetail->toArray();
        $this->assertModelData($bidSubmissionDetail->toArray(), $dbBidSubmissionDetail);
    }

    /**
     * @test update
     */
    public function test_update_bid_submission_detail()
    {
        $bidSubmissionDetail = factory(BidSubmissionDetail::class)->create();
        $fakeBidSubmissionDetail = factory(BidSubmissionDetail::class)->make()->toArray();

        $updatedBidSubmissionDetail = $this->bidSubmissionDetailRepo->update($fakeBidSubmissionDetail, $bidSubmissionDetail->id);

        $this->assertModelData($fakeBidSubmissionDetail, $updatedBidSubmissionDetail->toArray());
        $dbBidSubmissionDetail = $this->bidSubmissionDetailRepo->find($bidSubmissionDetail->id);
        $this->assertModelData($fakeBidSubmissionDetail, $dbBidSubmissionDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_bid_submission_detail()
    {
        $bidSubmissionDetail = factory(BidSubmissionDetail::class)->create();

        $resp = $this->bidSubmissionDetailRepo->delete($bidSubmissionDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(BidSubmissionDetail::find($bidSubmissionDetail->id), 'BidSubmissionDetail should not exist in DB');
    }
}
