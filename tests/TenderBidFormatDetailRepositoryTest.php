<?php namespace Tests\Repositories;

use App\Models\TenderBidFormatDetail;
use App\Repositories\TenderBidFormatDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderBidFormatDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderBidFormatDetailRepository
     */
    protected $tenderBidFormatDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderBidFormatDetailRepo = \App::make(TenderBidFormatDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_bid_format_detail()
    {
        $tenderBidFormatDetail = factory(TenderBidFormatDetail::class)->make()->toArray();

        $createdTenderBidFormatDetail = $this->tenderBidFormatDetailRepo->create($tenderBidFormatDetail);

        $createdTenderBidFormatDetail = $createdTenderBidFormatDetail->toArray();
        $this->assertArrayHasKey('id', $createdTenderBidFormatDetail);
        $this->assertNotNull($createdTenderBidFormatDetail['id'], 'Created TenderBidFormatDetail must have id specified');
        $this->assertNotNull(TenderBidFormatDetail::find($createdTenderBidFormatDetail['id']), 'TenderBidFormatDetail with given id must be in DB');
        $this->assertModelData($tenderBidFormatDetail, $createdTenderBidFormatDetail);
    }

    /**
     * @test read
     */
    public function test_read_tender_bid_format_detail()
    {
        $tenderBidFormatDetail = factory(TenderBidFormatDetail::class)->create();

        $dbTenderBidFormatDetail = $this->tenderBidFormatDetailRepo->find($tenderBidFormatDetail->id);

        $dbTenderBidFormatDetail = $dbTenderBidFormatDetail->toArray();
        $this->assertModelData($tenderBidFormatDetail->toArray(), $dbTenderBidFormatDetail);
    }

    /**
     * @test update
     */
    public function test_update_tender_bid_format_detail()
    {
        $tenderBidFormatDetail = factory(TenderBidFormatDetail::class)->create();
        $fakeTenderBidFormatDetail = factory(TenderBidFormatDetail::class)->make()->toArray();

        $updatedTenderBidFormatDetail = $this->tenderBidFormatDetailRepo->update($fakeTenderBidFormatDetail, $tenderBidFormatDetail->id);

        $this->assertModelData($fakeTenderBidFormatDetail, $updatedTenderBidFormatDetail->toArray());
        $dbTenderBidFormatDetail = $this->tenderBidFormatDetailRepo->find($tenderBidFormatDetail->id);
        $this->assertModelData($fakeTenderBidFormatDetail, $dbTenderBidFormatDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_bid_format_detail()
    {
        $tenderBidFormatDetail = factory(TenderBidFormatDetail::class)->create();

        $resp = $this->tenderBidFormatDetailRepo->delete($tenderBidFormatDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderBidFormatDetail::find($tenderBidFormatDetail->id), 'TenderBidFormatDetail should not exist in DB');
    }
}
