<?php

use App\Models\ProcumentOrder;
use App\Repositories\ProcumentOrderRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProcumentOrderRepositoryTest extends TestCase
{
    use MakeProcumentOrderTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ProcumentOrderRepository
     */
    protected $procumentOrderRepo;

    public function setUp()
    {
        parent::setUp();
        $this->procumentOrderRepo = App::make(ProcumentOrderRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateProcumentOrder()
    {
        $procumentOrder = $this->fakeProcumentOrderData();
        $createdProcumentOrder = $this->procumentOrderRepo->create($procumentOrder);
        $createdProcumentOrder = $createdProcumentOrder->toArray();
        $this->assertArrayHasKey('id', $createdProcumentOrder);
        $this->assertNotNull($createdProcumentOrder['id'], 'Created ProcumentOrder must have id specified');
        $this->assertNotNull(ProcumentOrder::find($createdProcumentOrder['id']), 'ProcumentOrder with given id must be in DB');
        $this->assertModelData($procumentOrder, $createdProcumentOrder);
    }

    /**
     * @test read
     */
    public function testReadProcumentOrder()
    {
        $procumentOrder = $this->makeProcumentOrder();
        $dbProcumentOrder = $this->procumentOrderRepo->find($procumentOrder->id);
        $dbProcumentOrder = $dbProcumentOrder->toArray();
        $this->assertModelData($procumentOrder->toArray(), $dbProcumentOrder);
    }

    /**
     * @test update
     */
    public function testUpdateProcumentOrder()
    {
        $procumentOrder = $this->makeProcumentOrder();
        $fakeProcumentOrder = $this->fakeProcumentOrderData();
        $updatedProcumentOrder = $this->procumentOrderRepo->update($fakeProcumentOrder, $procumentOrder->id);
        $this->assertModelData($fakeProcumentOrder, $updatedProcumentOrder->toArray());
        $dbProcumentOrder = $this->procumentOrderRepo->find($procumentOrder->id);
        $this->assertModelData($fakeProcumentOrder, $dbProcumentOrder->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteProcumentOrder()
    {
        $procumentOrder = $this->makeProcumentOrder();
        $resp = $this->procumentOrderRepo->delete($procumentOrder->id);
        $this->assertTrue($resp);
        $this->assertNull(ProcumentOrder::find($procumentOrder->id), 'ProcumentOrder should not exist in DB');
    }
}
