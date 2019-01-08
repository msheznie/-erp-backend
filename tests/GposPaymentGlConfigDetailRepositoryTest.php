<?php

use App\Models\GposPaymentGlConfigDetail;
use App\Repositories\GposPaymentGlConfigDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GposPaymentGlConfigDetailRepositoryTest extends TestCase
{
    use MakeGposPaymentGlConfigDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var GposPaymentGlConfigDetailRepository
     */
    protected $gposPaymentGlConfigDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->gposPaymentGlConfigDetailRepo = App::make(GposPaymentGlConfigDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateGposPaymentGlConfigDetail()
    {
        $gposPaymentGlConfigDetail = $this->fakeGposPaymentGlConfigDetailData();
        $createdGposPaymentGlConfigDetail = $this->gposPaymentGlConfigDetailRepo->create($gposPaymentGlConfigDetail);
        $createdGposPaymentGlConfigDetail = $createdGposPaymentGlConfigDetail->toArray();
        $this->assertArrayHasKey('id', $createdGposPaymentGlConfigDetail);
        $this->assertNotNull($createdGposPaymentGlConfigDetail['id'], 'Created GposPaymentGlConfigDetail must have id specified');
        $this->assertNotNull(GposPaymentGlConfigDetail::find($createdGposPaymentGlConfigDetail['id']), 'GposPaymentGlConfigDetail with given id must be in DB');
        $this->assertModelData($gposPaymentGlConfigDetail, $createdGposPaymentGlConfigDetail);
    }

    /**
     * @test read
     */
    public function testReadGposPaymentGlConfigDetail()
    {
        $gposPaymentGlConfigDetail = $this->makeGposPaymentGlConfigDetail();
        $dbGposPaymentGlConfigDetail = $this->gposPaymentGlConfigDetailRepo->find($gposPaymentGlConfigDetail->id);
        $dbGposPaymentGlConfigDetail = $dbGposPaymentGlConfigDetail->toArray();
        $this->assertModelData($gposPaymentGlConfigDetail->toArray(), $dbGposPaymentGlConfigDetail);
    }

    /**
     * @test update
     */
    public function testUpdateGposPaymentGlConfigDetail()
    {
        $gposPaymentGlConfigDetail = $this->makeGposPaymentGlConfigDetail();
        $fakeGposPaymentGlConfigDetail = $this->fakeGposPaymentGlConfigDetailData();
        $updatedGposPaymentGlConfigDetail = $this->gposPaymentGlConfigDetailRepo->update($fakeGposPaymentGlConfigDetail, $gposPaymentGlConfigDetail->id);
        $this->assertModelData($fakeGposPaymentGlConfigDetail, $updatedGposPaymentGlConfigDetail->toArray());
        $dbGposPaymentGlConfigDetail = $this->gposPaymentGlConfigDetailRepo->find($gposPaymentGlConfigDetail->id);
        $this->assertModelData($fakeGposPaymentGlConfigDetail, $dbGposPaymentGlConfigDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteGposPaymentGlConfigDetail()
    {
        $gposPaymentGlConfigDetail = $this->makeGposPaymentGlConfigDetail();
        $resp = $this->gposPaymentGlConfigDetailRepo->delete($gposPaymentGlConfigDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(GposPaymentGlConfigDetail::find($gposPaymentGlConfigDetail->id), 'GposPaymentGlConfigDetail should not exist in DB');
    }
}
