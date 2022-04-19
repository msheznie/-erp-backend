<?php namespace Tests\Repositories;

use App\Models\TenderBidClarifications;
use App\Repositories\TenderBidClarificationsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderBidClarificationsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderBidClarificationsRepository
     */
    protected $tenderBidClarificationsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderBidClarificationsRepo = \App::make(TenderBidClarificationsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_bid_clarifications()
    {
        $tenderBidClarifications = factory(TenderBidClarifications::class)->make()->toArray();

        $createdTenderBidClarifications = $this->tenderBidClarificationsRepo->create($tenderBidClarifications);

        $createdTenderBidClarifications = $createdTenderBidClarifications->toArray();
        $this->assertArrayHasKey('id', $createdTenderBidClarifications);
        $this->assertNotNull($createdTenderBidClarifications['id'], 'Created TenderBidClarifications must have id specified');
        $this->assertNotNull(TenderBidClarifications::find($createdTenderBidClarifications['id']), 'TenderBidClarifications with given id must be in DB');
        $this->assertModelData($tenderBidClarifications, $createdTenderBidClarifications);
    }

    /**
     * @test read
     */
    public function test_read_tender_bid_clarifications()
    {
        $tenderBidClarifications = factory(TenderBidClarifications::class)->create();

        $dbTenderBidClarifications = $this->tenderBidClarificationsRepo->find($tenderBidClarifications->id);

        $dbTenderBidClarifications = $dbTenderBidClarifications->toArray();
        $this->assertModelData($tenderBidClarifications->toArray(), $dbTenderBidClarifications);
    }

    /**
     * @test update
     */
    public function test_update_tender_bid_clarifications()
    {
        $tenderBidClarifications = factory(TenderBidClarifications::class)->create();
        $fakeTenderBidClarifications = factory(TenderBidClarifications::class)->make()->toArray();

        $updatedTenderBidClarifications = $this->tenderBidClarificationsRepo->update($fakeTenderBidClarifications, $tenderBidClarifications->id);

        $this->assertModelData($fakeTenderBidClarifications, $updatedTenderBidClarifications->toArray());
        $dbTenderBidClarifications = $this->tenderBidClarificationsRepo->find($tenderBidClarifications->id);
        $this->assertModelData($fakeTenderBidClarifications, $dbTenderBidClarifications->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_bid_clarifications()
    {
        $tenderBidClarifications = factory(TenderBidClarifications::class)->create();

        $resp = $this->tenderBidClarificationsRepo->delete($tenderBidClarifications->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderBidClarifications::find($tenderBidClarifications->id), 'TenderBidClarifications should not exist in DB');
    }
}
