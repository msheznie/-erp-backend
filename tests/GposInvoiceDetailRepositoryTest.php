<?php

use App\Models\GposInvoiceDetail;
use App\Repositories\GposInvoiceDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GposInvoiceDetailRepositoryTest extends TestCase
{
    use MakeGposInvoiceDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var GposInvoiceDetailRepository
     */
    protected $gposInvoiceDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->gposInvoiceDetailRepo = App::make(GposInvoiceDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateGposInvoiceDetail()
    {
        $gposInvoiceDetail = $this->fakeGposInvoiceDetailData();
        $createdGposInvoiceDetail = $this->gposInvoiceDetailRepo->create($gposInvoiceDetail);
        $createdGposInvoiceDetail = $createdGposInvoiceDetail->toArray();
        $this->assertArrayHasKey('id', $createdGposInvoiceDetail);
        $this->assertNotNull($createdGposInvoiceDetail['id'], 'Created GposInvoiceDetail must have id specified');
        $this->assertNotNull(GposInvoiceDetail::find($createdGposInvoiceDetail['id']), 'GposInvoiceDetail with given id must be in DB');
        $this->assertModelData($gposInvoiceDetail, $createdGposInvoiceDetail);
    }

    /**
     * @test read
     */
    public function testReadGposInvoiceDetail()
    {
        $gposInvoiceDetail = $this->makeGposInvoiceDetail();
        $dbGposInvoiceDetail = $this->gposInvoiceDetailRepo->find($gposInvoiceDetail->id);
        $dbGposInvoiceDetail = $dbGposInvoiceDetail->toArray();
        $this->assertModelData($gposInvoiceDetail->toArray(), $dbGposInvoiceDetail);
    }

    /**
     * @test update
     */
    public function testUpdateGposInvoiceDetail()
    {
        $gposInvoiceDetail = $this->makeGposInvoiceDetail();
        $fakeGposInvoiceDetail = $this->fakeGposInvoiceDetailData();
        $updatedGposInvoiceDetail = $this->gposInvoiceDetailRepo->update($fakeGposInvoiceDetail, $gposInvoiceDetail->id);
        $this->assertModelData($fakeGposInvoiceDetail, $updatedGposInvoiceDetail->toArray());
        $dbGposInvoiceDetail = $this->gposInvoiceDetailRepo->find($gposInvoiceDetail->id);
        $this->assertModelData($fakeGposInvoiceDetail, $dbGposInvoiceDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteGposInvoiceDetail()
    {
        $gposInvoiceDetail = $this->makeGposInvoiceDetail();
        $resp = $this->gposInvoiceDetailRepo->delete($gposInvoiceDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(GposInvoiceDetail::find($gposInvoiceDetail->id), 'GposInvoiceDetail should not exist in DB');
    }
}
