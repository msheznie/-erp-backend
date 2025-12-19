<?php

use App\Models\LogisticShippingMode;
use App\Repositories\LogisticShippingModeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogisticShippingModeRepositoryTest extends TestCase
{
    use MakeLogisticShippingModeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var LogisticShippingModeRepository
     */
    protected $logisticShippingModeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->logisticShippingModeRepo = App::make(LogisticShippingModeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateLogisticShippingMode()
    {
        $logisticShippingMode = $this->fakeLogisticShippingModeData();
        $createdLogisticShippingMode = $this->logisticShippingModeRepo->create($logisticShippingMode);
        $createdLogisticShippingMode = $createdLogisticShippingMode->toArray();
        $this->assertArrayHasKey('id', $createdLogisticShippingMode);
        $this->assertNotNull($createdLogisticShippingMode['id'], 'Created LogisticShippingMode must have id specified');
        $this->assertNotNull(LogisticShippingMode::find($createdLogisticShippingMode['id']), 'LogisticShippingMode with given id must be in DB');
        $this->assertModelData($logisticShippingMode, $createdLogisticShippingMode);
    }

    /**
     * @test read
     */
    public function testReadLogisticShippingMode()
    {
        $logisticShippingMode = $this->makeLogisticShippingMode();
        $dbLogisticShippingMode = $this->logisticShippingModeRepo->find($logisticShippingMode->id);
        $dbLogisticShippingMode = $dbLogisticShippingMode->toArray();
        $this->assertModelData($logisticShippingMode->toArray(), $dbLogisticShippingMode);
    }

    /**
     * @test update
     */
    public function testUpdateLogisticShippingMode()
    {
        $logisticShippingMode = $this->makeLogisticShippingMode();
        $fakeLogisticShippingMode = $this->fakeLogisticShippingModeData();
        $updatedLogisticShippingMode = $this->logisticShippingModeRepo->update($fakeLogisticShippingMode, $logisticShippingMode->id);
        $this->assertModelData($fakeLogisticShippingMode, $updatedLogisticShippingMode->toArray());
        $dbLogisticShippingMode = $this->logisticShippingModeRepo->find($logisticShippingMode->id);
        $this->assertModelData($fakeLogisticShippingMode, $dbLogisticShippingMode->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteLogisticShippingMode()
    {
        $logisticShippingMode = $this->makeLogisticShippingMode();
        $resp = $this->logisticShippingModeRepo->delete($logisticShippingMode->id);
        $this->assertTrue($resp);
        $this->assertNull(LogisticShippingMode::find($logisticShippingMode->id), 'LogisticShippingMode should not exist in DB');
    }
}
