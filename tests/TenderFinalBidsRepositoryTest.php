<?php namespace Tests\Repositories;

use App\Models\TenderFinalBids;
use App\Repositories\TenderFinalBidsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderFinalBidsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderFinalBidsRepository
     */
    protected $tenderFinalBidsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderFinalBidsRepo = \App::make(TenderFinalBidsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_final_bids()
    {
        $tenderFinalBids = factory(TenderFinalBids::class)->make()->toArray();

        $createdTenderFinalBids = $this->tenderFinalBidsRepo->create($tenderFinalBids);

        $createdTenderFinalBids = $createdTenderFinalBids->toArray();
        $this->assertArrayHasKey('id', $createdTenderFinalBids);
        $this->assertNotNull($createdTenderFinalBids['id'], 'Created TenderFinalBids must have id specified');
        $this->assertNotNull(TenderFinalBids::find($createdTenderFinalBids['id']), 'TenderFinalBids with given id must be in DB');
        $this->assertModelData($tenderFinalBids, $createdTenderFinalBids);
    }

    /**
     * @test read
     */
    public function test_read_tender_final_bids()
    {
        $tenderFinalBids = factory(TenderFinalBids::class)->create();

        $dbTenderFinalBids = $this->tenderFinalBidsRepo->find($tenderFinalBids->id);

        $dbTenderFinalBids = $dbTenderFinalBids->toArray();
        $this->assertModelData($tenderFinalBids->toArray(), $dbTenderFinalBids);
    }

    /**
     * @test update
     */
    public function test_update_tender_final_bids()
    {
        $tenderFinalBids = factory(TenderFinalBids::class)->create();
        $fakeTenderFinalBids = factory(TenderFinalBids::class)->make()->toArray();

        $updatedTenderFinalBids = $this->tenderFinalBidsRepo->update($fakeTenderFinalBids, $tenderFinalBids->id);

        $this->assertModelData($fakeTenderFinalBids, $updatedTenderFinalBids->toArray());
        $dbTenderFinalBids = $this->tenderFinalBidsRepo->find($tenderFinalBids->id);
        $this->assertModelData($fakeTenderFinalBids, $dbTenderFinalBids->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_final_bids()
    {
        $tenderFinalBids = factory(TenderFinalBids::class)->create();

        $resp = $this->tenderFinalBidsRepo->delete($tenderFinalBids->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderFinalBids::find($tenderFinalBids->id), 'TenderFinalBids should not exist in DB');
    }
}
