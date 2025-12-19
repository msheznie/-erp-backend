<?php

use App\Models\LogisticDetails;
use App\Repositories\LogisticDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogisticDetailsRepositoryTest extends TestCase
{
    use MakeLogisticDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var LogisticDetailsRepository
     */
    protected $logisticDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->logisticDetailsRepo = App::make(LogisticDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateLogisticDetails()
    {
        $logisticDetails = $this->fakeLogisticDetailsData();
        $createdLogisticDetails = $this->logisticDetailsRepo->create($logisticDetails);
        $createdLogisticDetails = $createdLogisticDetails->toArray();
        $this->assertArrayHasKey('id', $createdLogisticDetails);
        $this->assertNotNull($createdLogisticDetails['id'], 'Created LogisticDetails must have id specified');
        $this->assertNotNull(LogisticDetails::find($createdLogisticDetails['id']), 'LogisticDetails with given id must be in DB');
        $this->assertModelData($logisticDetails, $createdLogisticDetails);
    }

    /**
     * @test read
     */
    public function testReadLogisticDetails()
    {
        $logisticDetails = $this->makeLogisticDetails();
        $dbLogisticDetails = $this->logisticDetailsRepo->find($logisticDetails->id);
        $dbLogisticDetails = $dbLogisticDetails->toArray();
        $this->assertModelData($logisticDetails->toArray(), $dbLogisticDetails);
    }

    /**
     * @test update
     */
    public function testUpdateLogisticDetails()
    {
        $logisticDetails = $this->makeLogisticDetails();
        $fakeLogisticDetails = $this->fakeLogisticDetailsData();
        $updatedLogisticDetails = $this->logisticDetailsRepo->update($fakeLogisticDetails, $logisticDetails->id);
        $this->assertModelData($fakeLogisticDetails, $updatedLogisticDetails->toArray());
        $dbLogisticDetails = $this->logisticDetailsRepo->find($logisticDetails->id);
        $this->assertModelData($fakeLogisticDetails, $dbLogisticDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteLogisticDetails()
    {
        $logisticDetails = $this->makeLogisticDetails();
        $resp = $this->logisticDetailsRepo->delete($logisticDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(LogisticDetails::find($logisticDetails->id), 'LogisticDetails should not exist in DB');
    }
}
