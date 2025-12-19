<?php namespace Tests\Repositories;

use App\Models\TenderBidFormatMaster;
use App\Repositories\TenderBidFormatMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderBidFormatMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderBidFormatMasterRepository
     */
    protected $tenderBidFormatMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderBidFormatMasterRepo = \App::make(TenderBidFormatMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_bid_format_master()
    {
        $tenderBidFormatMaster = factory(TenderBidFormatMaster::class)->make()->toArray();

        $createdTenderBidFormatMaster = $this->tenderBidFormatMasterRepo->create($tenderBidFormatMaster);

        $createdTenderBidFormatMaster = $createdTenderBidFormatMaster->toArray();
        $this->assertArrayHasKey('id', $createdTenderBidFormatMaster);
        $this->assertNotNull($createdTenderBidFormatMaster['id'], 'Created TenderBidFormatMaster must have id specified');
        $this->assertNotNull(TenderBidFormatMaster::find($createdTenderBidFormatMaster['id']), 'TenderBidFormatMaster with given id must be in DB');
        $this->assertModelData($tenderBidFormatMaster, $createdTenderBidFormatMaster);
    }

    /**
     * @test read
     */
    public function test_read_tender_bid_format_master()
    {
        $tenderBidFormatMaster = factory(TenderBidFormatMaster::class)->create();

        $dbTenderBidFormatMaster = $this->tenderBidFormatMasterRepo->find($tenderBidFormatMaster->id);

        $dbTenderBidFormatMaster = $dbTenderBidFormatMaster->toArray();
        $this->assertModelData($tenderBidFormatMaster->toArray(), $dbTenderBidFormatMaster);
    }

    /**
     * @test update
     */
    public function test_update_tender_bid_format_master()
    {
        $tenderBidFormatMaster = factory(TenderBidFormatMaster::class)->create();
        $fakeTenderBidFormatMaster = factory(TenderBidFormatMaster::class)->make()->toArray();

        $updatedTenderBidFormatMaster = $this->tenderBidFormatMasterRepo->update($fakeTenderBidFormatMaster, $tenderBidFormatMaster->id);

        $this->assertModelData($fakeTenderBidFormatMaster, $updatedTenderBidFormatMaster->toArray());
        $dbTenderBidFormatMaster = $this->tenderBidFormatMasterRepo->find($tenderBidFormatMaster->id);
        $this->assertModelData($fakeTenderBidFormatMaster, $dbTenderBidFormatMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_bid_format_master()
    {
        $tenderBidFormatMaster = factory(TenderBidFormatMaster::class)->create();

        $resp = $this->tenderBidFormatMasterRepo->delete($tenderBidFormatMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderBidFormatMaster::find($tenderBidFormatMaster->id), 'TenderBidFormatMaster should not exist in DB');
    }
}
