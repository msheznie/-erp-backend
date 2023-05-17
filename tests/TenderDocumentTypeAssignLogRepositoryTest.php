<?php namespace Tests\Repositories;

use App\Models\TenderDocumentTypeAssignLog;
use App\Repositories\TenderDocumentTypeAssignLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderDocumentTypeAssignLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderDocumentTypeAssignLogRepository
     */
    protected $tenderDocumentTypeAssignLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderDocumentTypeAssignLogRepo = \App::make(TenderDocumentTypeAssignLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_document_type_assign_log()
    {
        $tenderDocumentTypeAssignLog = factory(TenderDocumentTypeAssignLog::class)->make()->toArray();

        $createdTenderDocumentTypeAssignLog = $this->tenderDocumentTypeAssignLogRepo->create($tenderDocumentTypeAssignLog);

        $createdTenderDocumentTypeAssignLog = $createdTenderDocumentTypeAssignLog->toArray();
        $this->assertArrayHasKey('id', $createdTenderDocumentTypeAssignLog);
        $this->assertNotNull($createdTenderDocumentTypeAssignLog['id'], 'Created TenderDocumentTypeAssignLog must have id specified');
        $this->assertNotNull(TenderDocumentTypeAssignLog::find($createdTenderDocumentTypeAssignLog['id']), 'TenderDocumentTypeAssignLog with given id must be in DB');
        $this->assertModelData($tenderDocumentTypeAssignLog, $createdTenderDocumentTypeAssignLog);
    }

    /**
     * @test read
     */
    public function test_read_tender_document_type_assign_log()
    {
        $tenderDocumentTypeAssignLog = factory(TenderDocumentTypeAssignLog::class)->create();

        $dbTenderDocumentTypeAssignLog = $this->tenderDocumentTypeAssignLogRepo->find($tenderDocumentTypeAssignLog->id);

        $dbTenderDocumentTypeAssignLog = $dbTenderDocumentTypeAssignLog->toArray();
        $this->assertModelData($tenderDocumentTypeAssignLog->toArray(), $dbTenderDocumentTypeAssignLog);
    }

    /**
     * @test update
     */
    public function test_update_tender_document_type_assign_log()
    {
        $tenderDocumentTypeAssignLog = factory(TenderDocumentTypeAssignLog::class)->create();
        $fakeTenderDocumentTypeAssignLog = factory(TenderDocumentTypeAssignLog::class)->make()->toArray();

        $updatedTenderDocumentTypeAssignLog = $this->tenderDocumentTypeAssignLogRepo->update($fakeTenderDocumentTypeAssignLog, $tenderDocumentTypeAssignLog->id);

        $this->assertModelData($fakeTenderDocumentTypeAssignLog, $updatedTenderDocumentTypeAssignLog->toArray());
        $dbTenderDocumentTypeAssignLog = $this->tenderDocumentTypeAssignLogRepo->find($tenderDocumentTypeAssignLog->id);
        $this->assertModelData($fakeTenderDocumentTypeAssignLog, $dbTenderDocumentTypeAssignLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_document_type_assign_log()
    {
        $tenderDocumentTypeAssignLog = factory(TenderDocumentTypeAssignLog::class)->create();

        $resp = $this->tenderDocumentTypeAssignLogRepo->delete($tenderDocumentTypeAssignLog->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderDocumentTypeAssignLog::find($tenderDocumentTypeAssignLog->id), 'TenderDocumentTypeAssignLog should not exist in DB');
    }
}
