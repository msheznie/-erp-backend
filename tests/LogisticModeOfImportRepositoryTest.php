<?php

use App\Models\LogisticModeOfImport;
use App\Repositories\LogisticModeOfImportRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogisticModeOfImportRepositoryTest extends TestCase
{
    use MakeLogisticModeOfImportTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var LogisticModeOfImportRepository
     */
    protected $logisticModeOfImportRepo;

    public function setUp()
    {
        parent::setUp();
        $this->logisticModeOfImportRepo = App::make(LogisticModeOfImportRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateLogisticModeOfImport()
    {
        $logisticModeOfImport = $this->fakeLogisticModeOfImportData();
        $createdLogisticModeOfImport = $this->logisticModeOfImportRepo->create($logisticModeOfImport);
        $createdLogisticModeOfImport = $createdLogisticModeOfImport->toArray();
        $this->assertArrayHasKey('id', $createdLogisticModeOfImport);
        $this->assertNotNull($createdLogisticModeOfImport['id'], 'Created LogisticModeOfImport must have id specified');
        $this->assertNotNull(LogisticModeOfImport::find($createdLogisticModeOfImport['id']), 'LogisticModeOfImport with given id must be in DB');
        $this->assertModelData($logisticModeOfImport, $createdLogisticModeOfImport);
    }

    /**
     * @test read
     */
    public function testReadLogisticModeOfImport()
    {
        $logisticModeOfImport = $this->makeLogisticModeOfImport();
        $dbLogisticModeOfImport = $this->logisticModeOfImportRepo->find($logisticModeOfImport->id);
        $dbLogisticModeOfImport = $dbLogisticModeOfImport->toArray();
        $this->assertModelData($logisticModeOfImport->toArray(), $dbLogisticModeOfImport);
    }

    /**
     * @test update
     */
    public function testUpdateLogisticModeOfImport()
    {
        $logisticModeOfImport = $this->makeLogisticModeOfImport();
        $fakeLogisticModeOfImport = $this->fakeLogisticModeOfImportData();
        $updatedLogisticModeOfImport = $this->logisticModeOfImportRepo->update($fakeLogisticModeOfImport, $logisticModeOfImport->id);
        $this->assertModelData($fakeLogisticModeOfImport, $updatedLogisticModeOfImport->toArray());
        $dbLogisticModeOfImport = $this->logisticModeOfImportRepo->find($logisticModeOfImport->id);
        $this->assertModelData($fakeLogisticModeOfImport, $dbLogisticModeOfImport->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteLogisticModeOfImport()
    {
        $logisticModeOfImport = $this->makeLogisticModeOfImport();
        $resp = $this->logisticModeOfImportRepo->delete($logisticModeOfImport->id);
        $this->assertTrue($resp);
        $this->assertNull(LogisticModeOfImport::find($logisticModeOfImport->id), 'LogisticModeOfImport should not exist in DB');
    }
}
