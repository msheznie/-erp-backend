<?php namespace Tests\Repositories;

use App\Models\BidDocumentVerification;
use App\Repositories\BidDocumentVerificationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BidDocumentVerificationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BidDocumentVerificationRepository
     */
    protected $bidDocumentVerificationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->bidDocumentVerificationRepo = \App::make(BidDocumentVerificationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_bid_document_verification()
    {
        $bidDocumentVerification = factory(BidDocumentVerification::class)->make()->toArray();

        $createdBidDocumentVerification = $this->bidDocumentVerificationRepo->create($bidDocumentVerification);

        $createdBidDocumentVerification = $createdBidDocumentVerification->toArray();
        $this->assertArrayHasKey('id', $createdBidDocumentVerification);
        $this->assertNotNull($createdBidDocumentVerification['id'], 'Created BidDocumentVerification must have id specified');
        $this->assertNotNull(BidDocumentVerification::find($createdBidDocumentVerification['id']), 'BidDocumentVerification with given id must be in DB');
        $this->assertModelData($bidDocumentVerification, $createdBidDocumentVerification);
    }

    /**
     * @test read
     */
    public function test_read_bid_document_verification()
    {
        $bidDocumentVerification = factory(BidDocumentVerification::class)->create();

        $dbBidDocumentVerification = $this->bidDocumentVerificationRepo->find($bidDocumentVerification->id);

        $dbBidDocumentVerification = $dbBidDocumentVerification->toArray();
        $this->assertModelData($bidDocumentVerification->toArray(), $dbBidDocumentVerification);
    }

    /**
     * @test update
     */
    public function test_update_bid_document_verification()
    {
        $bidDocumentVerification = factory(BidDocumentVerification::class)->create();
        $fakeBidDocumentVerification = factory(BidDocumentVerification::class)->make()->toArray();

        $updatedBidDocumentVerification = $this->bidDocumentVerificationRepo->update($fakeBidDocumentVerification, $bidDocumentVerification->id);

        $this->assertModelData($fakeBidDocumentVerification, $updatedBidDocumentVerification->toArray());
        $dbBidDocumentVerification = $this->bidDocumentVerificationRepo->find($bidDocumentVerification->id);
        $this->assertModelData($fakeBidDocumentVerification, $dbBidDocumentVerification->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_bid_document_verification()
    {
        $bidDocumentVerification = factory(BidDocumentVerification::class)->create();

        $resp = $this->bidDocumentVerificationRepo->delete($bidDocumentVerification->id);

        $this->assertTrue($resp);
        $this->assertNull(BidDocumentVerification::find($bidDocumentVerification->id), 'BidDocumentVerification should not exist in DB');
    }
}
