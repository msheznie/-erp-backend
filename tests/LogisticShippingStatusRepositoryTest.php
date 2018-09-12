<?php

use App\Models\LogisticShippingStatus;
use App\Repositories\LogisticShippingStatusRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogisticShippingStatusRepositoryTest extends TestCase
{
    use MakeLogisticShippingStatusTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var LogisticShippingStatusRepository
     */
    protected $logisticShippingStatusRepo;

    public function setUp()
    {
        parent::setUp();
        $this->logisticShippingStatusRepo = App::make(LogisticShippingStatusRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateLogisticShippingStatus()
    {
        $logisticShippingStatus = $this->fakeLogisticShippingStatusData();
        $createdLogisticShippingStatus = $this->logisticShippingStatusRepo->create($logisticShippingStatus);
        $createdLogisticShippingStatus = $createdLogisticShippingStatus->toArray();
        $this->assertArrayHasKey('id', $createdLogisticShippingStatus);
        $this->assertNotNull($createdLogisticShippingStatus['id'], 'Created LogisticShippingStatus must have id specified');
        $this->assertNotNull(LogisticShippingStatus::find($createdLogisticShippingStatus['id']), 'LogisticShippingStatus with given id must be in DB');
        $this->assertModelData($logisticShippingStatus, $createdLogisticShippingStatus);
    }

    /**
     * @test read
     */
    public function testReadLogisticShippingStatus()
    {
        $logisticShippingStatus = $this->makeLogisticShippingStatus();
        $dbLogisticShippingStatus = $this->logisticShippingStatusRepo->find($logisticShippingStatus->id);
        $dbLogisticShippingStatus = $dbLogisticShippingStatus->toArray();
        $this->assertModelData($logisticShippingStatus->toArray(), $dbLogisticShippingStatus);
    }

    /**
     * @test update
     */
    public function testUpdateLogisticShippingStatus()
    {
        $logisticShippingStatus = $this->makeLogisticShippingStatus();
        $fakeLogisticShippingStatus = $this->fakeLogisticShippingStatusData();
        $updatedLogisticShippingStatus = $this->logisticShippingStatusRepo->update($fakeLogisticShippingStatus, $logisticShippingStatus->id);
        $this->assertModelData($fakeLogisticShippingStatus, $updatedLogisticShippingStatus->toArray());
        $dbLogisticShippingStatus = $this->logisticShippingStatusRepo->find($logisticShippingStatus->id);
        $this->assertModelData($fakeLogisticShippingStatus, $dbLogisticShippingStatus->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteLogisticShippingStatus()
    {
        $logisticShippingStatus = $this->makeLogisticShippingStatus();
        $resp = $this->logisticShippingStatusRepo->delete($logisticShippingStatus->id);
        $this->assertTrue($resp);
        $this->assertNull(LogisticShippingStatus::find($logisticShippingStatus->id), 'LogisticShippingStatus should not exist in DB');
    }
}
