<?php

use App\Models\Logistic;
use App\Repositories\LogisticRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogisticRepositoryTest extends TestCase
{
    use MakeLogisticTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var LogisticRepository
     */
    protected $logisticRepo;

    public function setUp()
    {
        parent::setUp();
        $this->logisticRepo = App::make(LogisticRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateLogistic()
    {
        $logistic = $this->fakeLogisticData();
        $createdLogistic = $this->logisticRepo->create($logistic);
        $createdLogistic = $createdLogistic->toArray();
        $this->assertArrayHasKey('id', $createdLogistic);
        $this->assertNotNull($createdLogistic['id'], 'Created Logistic must have id specified');
        $this->assertNotNull(Logistic::find($createdLogistic['id']), 'Logistic with given id must be in DB');
        $this->assertModelData($logistic, $createdLogistic);
    }

    /**
     * @test read
     */
    public function testReadLogistic()
    {
        $logistic = $this->makeLogistic();
        $dbLogistic = $this->logisticRepo->find($logistic->id);
        $dbLogistic = $dbLogistic->toArray();
        $this->assertModelData($logistic->toArray(), $dbLogistic);
    }

    /**
     * @test update
     */
    public function testUpdateLogistic()
    {
        $logistic = $this->makeLogistic();
        $fakeLogistic = $this->fakeLogisticData();
        $updatedLogistic = $this->logisticRepo->update($fakeLogistic, $logistic->id);
        $this->assertModelData($fakeLogistic, $updatedLogistic->toArray());
        $dbLogistic = $this->logisticRepo->find($logistic->id);
        $this->assertModelData($fakeLogistic, $dbLogistic->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteLogistic()
    {
        $logistic = $this->makeLogistic();
        $resp = $this->logisticRepo->delete($logistic->id);
        $this->assertTrue($resp);
        $this->assertNull(Logistic::find($logistic->id), 'Logistic should not exist in DB');
    }
}
