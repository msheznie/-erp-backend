<?php

use App\Models\LogisticStatus;
use App\Repositories\LogisticStatusRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogisticStatusRepositoryTest extends TestCase
{
    use MakeLogisticStatusTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var LogisticStatusRepository
     */
    protected $logisticStatusRepo;

    public function setUp()
    {
        parent::setUp();
        $this->logisticStatusRepo = App::make(LogisticStatusRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateLogisticStatus()
    {
        $logisticStatus = $this->fakeLogisticStatusData();
        $createdLogisticStatus = $this->logisticStatusRepo->create($logisticStatus);
        $createdLogisticStatus = $createdLogisticStatus->toArray();
        $this->assertArrayHasKey('id', $createdLogisticStatus);
        $this->assertNotNull($createdLogisticStatus['id'], 'Created LogisticStatus must have id specified');
        $this->assertNotNull(LogisticStatus::find($createdLogisticStatus['id']), 'LogisticStatus with given id must be in DB');
        $this->assertModelData($logisticStatus, $createdLogisticStatus);
    }

    /**
     * @test read
     */
    public function testReadLogisticStatus()
    {
        $logisticStatus = $this->makeLogisticStatus();
        $dbLogisticStatus = $this->logisticStatusRepo->find($logisticStatus->id);
        $dbLogisticStatus = $dbLogisticStatus->toArray();
        $this->assertModelData($logisticStatus->toArray(), $dbLogisticStatus);
    }

    /**
     * @test update
     */
    public function testUpdateLogisticStatus()
    {
        $logisticStatus = $this->makeLogisticStatus();
        $fakeLogisticStatus = $this->fakeLogisticStatusData();
        $updatedLogisticStatus = $this->logisticStatusRepo->update($fakeLogisticStatus, $logisticStatus->id);
        $this->assertModelData($fakeLogisticStatus, $updatedLogisticStatus->toArray());
        $dbLogisticStatus = $this->logisticStatusRepo->find($logisticStatus->id);
        $this->assertModelData($fakeLogisticStatus, $dbLogisticStatus->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteLogisticStatus()
    {
        $logisticStatus = $this->makeLogisticStatus();
        $resp = $this->logisticStatusRepo->delete($logisticStatus->id);
        $this->assertTrue($resp);
        $this->assertNull(LogisticStatus::find($logisticStatus->id), 'LogisticStatus should not exist in DB');
    }
}
