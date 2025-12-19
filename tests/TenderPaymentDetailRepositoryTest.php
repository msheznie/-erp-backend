<?php namespace Tests\Repositories;

use App\Models\TenderPaymentDetail;
use App\Repositories\TenderPaymentDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderPaymentDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderPaymentDetailRepository
     */
    protected $tenderPaymentDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderPaymentDetailRepo = \App::make(TenderPaymentDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_payment_detail()
    {
        $tenderPaymentDetail = factory(TenderPaymentDetail::class)->make()->toArray();

        $createdTenderPaymentDetail = $this->tenderPaymentDetailRepo->create($tenderPaymentDetail);

        $createdTenderPaymentDetail = $createdTenderPaymentDetail->toArray();
        $this->assertArrayHasKey('id', $createdTenderPaymentDetail);
        $this->assertNotNull($createdTenderPaymentDetail['id'], 'Created TenderPaymentDetail must have id specified');
        $this->assertNotNull(TenderPaymentDetail::find($createdTenderPaymentDetail['id']), 'TenderPaymentDetail with given id must be in DB');
        $this->assertModelData($tenderPaymentDetail, $createdTenderPaymentDetail);
    }

    /**
     * @test read
     */
    public function test_read_tender_payment_detail()
    {
        $tenderPaymentDetail = factory(TenderPaymentDetail::class)->create();

        $dbTenderPaymentDetail = $this->tenderPaymentDetailRepo->find($tenderPaymentDetail->id);

        $dbTenderPaymentDetail = $dbTenderPaymentDetail->toArray();
        $this->assertModelData($tenderPaymentDetail->toArray(), $dbTenderPaymentDetail);
    }

    /**
     * @test update
     */
    public function test_update_tender_payment_detail()
    {
        $tenderPaymentDetail = factory(TenderPaymentDetail::class)->create();
        $fakeTenderPaymentDetail = factory(TenderPaymentDetail::class)->make()->toArray();

        $updatedTenderPaymentDetail = $this->tenderPaymentDetailRepo->update($fakeTenderPaymentDetail, $tenderPaymentDetail->id);

        $this->assertModelData($fakeTenderPaymentDetail, $updatedTenderPaymentDetail->toArray());
        $dbTenderPaymentDetail = $this->tenderPaymentDetailRepo->find($tenderPaymentDetail->id);
        $this->assertModelData($fakeTenderPaymentDetail, $dbTenderPaymentDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_payment_detail()
    {
        $tenderPaymentDetail = factory(TenderPaymentDetail::class)->create();

        $resp = $this->tenderPaymentDetailRepo->delete($tenderPaymentDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderPaymentDetail::find($tenderPaymentDetail->id), 'TenderPaymentDetail should not exist in DB');
    }
}
