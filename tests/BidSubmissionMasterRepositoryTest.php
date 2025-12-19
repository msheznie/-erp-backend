<?php namespace Tests\Repositories;

use App\Models\BidSubmissionMaster;
use App\Repositories\BidSubmissionMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BidSubmissionMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BidSubmissionMasterRepository
     */
    protected $bidSubmissionMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->bidSubmissionMasterRepo = \App::make(BidSubmissionMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_bid_submission_master()
    {
        $bidSubmissionMaster = factory(BidSubmissionMaster::class)->make()->toArray();

        $createdBidSubmissionMaster = $this->bidSubmissionMasterRepo->create($bidSubmissionMaster);

        $createdBidSubmissionMaster = $createdBidSubmissionMaster->toArray();
        $this->assertArrayHasKey('id', $createdBidSubmissionMaster);
        $this->assertNotNull($createdBidSubmissionMaster['id'], 'Created BidSubmissionMaster must have id specified');
        $this->assertNotNull(BidSubmissionMaster::find($createdBidSubmissionMaster['id']), 'BidSubmissionMaster with given id must be in DB');
        $this->assertModelData($bidSubmissionMaster, $createdBidSubmissionMaster);
    }

    /**
     * @test read
     */
    public function test_read_bid_submission_master()
    {
        $bidSubmissionMaster = factory(BidSubmissionMaster::class)->create();

        $dbBidSubmissionMaster = $this->bidSubmissionMasterRepo->find($bidSubmissionMaster->id);

        $dbBidSubmissionMaster = $dbBidSubmissionMaster->toArray();
        $this->assertModelData($bidSubmissionMaster->toArray(), $dbBidSubmissionMaster);
    }

    /**
     * @test update
     */
    public function test_update_bid_submission_master()
    {
        $bidSubmissionMaster = factory(BidSubmissionMaster::class)->create();
        $fakeBidSubmissionMaster = factory(BidSubmissionMaster::class)->make()->toArray();

        $updatedBidSubmissionMaster = $this->bidSubmissionMasterRepo->update($fakeBidSubmissionMaster, $bidSubmissionMaster->id);

        $this->assertModelData($fakeBidSubmissionMaster, $updatedBidSubmissionMaster->toArray());
        $dbBidSubmissionMaster = $this->bidSubmissionMasterRepo->find($bidSubmissionMaster->id);
        $this->assertModelData($fakeBidSubmissionMaster, $dbBidSubmissionMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_bid_submission_master()
    {
        $bidSubmissionMaster = factory(BidSubmissionMaster::class)->create();

        $resp = $this->bidSubmissionMasterRepo->delete($bidSubmissionMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(BidSubmissionMaster::find($bidSubmissionMaster->id), 'BidSubmissionMaster should not exist in DB');
    }
}
