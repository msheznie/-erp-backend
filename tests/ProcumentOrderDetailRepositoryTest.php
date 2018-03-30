<?php

use App\Models\ProcumentOrderDetail;
use App\Repositories\ProcumentOrderDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProcumentOrderDetailRepositoryTest extends TestCase
{
    use MakeProcumentOrderDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ProcumentOrderDetailRepository
     */
    protected $procumentOrderDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->procumentOrderDetailRepo = App::make(ProcumentOrderDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateProcumentOrderDetail()
    {
        $procumentOrderDetail = $this->fakeProcumentOrderDetailData();
        $createdProcumentOrderDetail = $this->procumentOrderDetailRepo->create($procumentOrderDetail);
        $createdProcumentOrderDetail = $createdProcumentOrderDetail->toArray();
        $this->assertArrayHasKey('id', $createdProcumentOrderDetail);
        $this->assertNotNull($createdProcumentOrderDetail['id'], 'Created ProcumentOrderDetail must have id specified');
        $this->assertNotNull(ProcumentOrderDetail::find($createdProcumentOrderDetail['id']), 'ProcumentOrderDetail with given id must be in DB');
        $this->assertModelData($procumentOrderDetail, $createdProcumentOrderDetail);
    }

    /**
     * @test read
     */
    public function testReadProcumentOrderDetail()
    {
        $procumentOrderDetail = $this->makeProcumentOrderDetail();
        $dbProcumentOrderDetail = $this->procumentOrderDetailRepo->find($procumentOrderDetail->id);
        $dbProcumentOrderDetail = $dbProcumentOrderDetail->toArray();
        $this->assertModelData($procumentOrderDetail->toArray(), $dbProcumentOrderDetail);
    }

    /**
     * @test update
     */
    public function testUpdateProcumentOrderDetail()
    {
        $procumentOrderDetail = $this->makeProcumentOrderDetail();
        $fakeProcumentOrderDetail = $this->fakeProcumentOrderDetailData();
        $updatedProcumentOrderDetail = $this->procumentOrderDetailRepo->update($fakeProcumentOrderDetail, $procumentOrderDetail->id);
        $this->assertModelData($fakeProcumentOrderDetail, $updatedProcumentOrderDetail->toArray());
        $dbProcumentOrderDetail = $this->procumentOrderDetailRepo->find($procumentOrderDetail->id);
        $this->assertModelData($fakeProcumentOrderDetail, $dbProcumentOrderDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteProcumentOrderDetail()
    {
        $procumentOrderDetail = $this->makeProcumentOrderDetail();
        $resp = $this->procumentOrderDetailRepo->delete($procumentOrderDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(ProcumentOrderDetail::find($procumentOrderDetail->id), 'ProcumentOrderDetail should not exist in DB');
    }
}
