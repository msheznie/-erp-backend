<?php namespace Tests\Repositories;

use App\Models\TenderSupplierAssigneeEditLog;
use App\Repositories\TenderSupplierAssigneeEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderSupplierAssigneeEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderSupplierAssigneeEditLogRepository
     */
    protected $tenderSupplierAssigneeEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderSupplierAssigneeEditLogRepo = \App::make(TenderSupplierAssigneeEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_supplier_assignee_edit_log()
    {
        $tenderSupplierAssigneeEditLog = factory(TenderSupplierAssigneeEditLog::class)->make()->toArray();

        $createdTenderSupplierAssigneeEditLog = $this->tenderSupplierAssigneeEditLogRepo->create($tenderSupplierAssigneeEditLog);

        $createdTenderSupplierAssigneeEditLog = $createdTenderSupplierAssigneeEditLog->toArray();
        $this->assertArrayHasKey('id', $createdTenderSupplierAssigneeEditLog);
        $this->assertNotNull($createdTenderSupplierAssigneeEditLog['id'], 'Created TenderSupplierAssigneeEditLog must have id specified');
        $this->assertNotNull(TenderSupplierAssigneeEditLog::find($createdTenderSupplierAssigneeEditLog['id']), 'TenderSupplierAssigneeEditLog with given id must be in DB');
        $this->assertModelData($tenderSupplierAssigneeEditLog, $createdTenderSupplierAssigneeEditLog);
    }

    /**
     * @test read
     */
    public function test_read_tender_supplier_assignee_edit_log()
    {
        $tenderSupplierAssigneeEditLog = factory(TenderSupplierAssigneeEditLog::class)->create();

        $dbTenderSupplierAssigneeEditLog = $this->tenderSupplierAssigneeEditLogRepo->find($tenderSupplierAssigneeEditLog->id);

        $dbTenderSupplierAssigneeEditLog = $dbTenderSupplierAssigneeEditLog->toArray();
        $this->assertModelData($tenderSupplierAssigneeEditLog->toArray(), $dbTenderSupplierAssigneeEditLog);
    }

    /**
     * @test update
     */
    public function test_update_tender_supplier_assignee_edit_log()
    {
        $tenderSupplierAssigneeEditLog = factory(TenderSupplierAssigneeEditLog::class)->create();
        $fakeTenderSupplierAssigneeEditLog = factory(TenderSupplierAssigneeEditLog::class)->make()->toArray();

        $updatedTenderSupplierAssigneeEditLog = $this->tenderSupplierAssigneeEditLogRepo->update($fakeTenderSupplierAssigneeEditLog, $tenderSupplierAssigneeEditLog->id);

        $this->assertModelData($fakeTenderSupplierAssigneeEditLog, $updatedTenderSupplierAssigneeEditLog->toArray());
        $dbTenderSupplierAssigneeEditLog = $this->tenderSupplierAssigneeEditLogRepo->find($tenderSupplierAssigneeEditLog->id);
        $this->assertModelData($fakeTenderSupplierAssigneeEditLog, $dbTenderSupplierAssigneeEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_supplier_assignee_edit_log()
    {
        $tenderSupplierAssigneeEditLog = factory(TenderSupplierAssigneeEditLog::class)->create();

        $resp = $this->tenderSupplierAssigneeEditLogRepo->delete($tenderSupplierAssigneeEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderSupplierAssigneeEditLog::find($tenderSupplierAssigneeEditLog->id), 'TenderSupplierAssigneeEditLog should not exist in DB');
    }
}
