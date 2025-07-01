<?php namespace Tests\Repositories;

use App\Models\TenderDepartmentEditLog;
use App\Repositories\TenderDepartmentEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderDepartmentEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderDepartmentEditLogRepository
     */
    protected $tenderDepartmentEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderDepartmentEditLogRepo = \App::make(TenderDepartmentEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_department_edit_log()
    {
        $tenderDepartmentEditLog = factory(TenderDepartmentEditLog::class)->make()->toArray();

        $createdTenderDepartmentEditLog = $this->tenderDepartmentEditLogRepo->create($tenderDepartmentEditLog);

        $createdTenderDepartmentEditLog = $createdTenderDepartmentEditLog->toArray();
        $this->assertArrayHasKey('id', $createdTenderDepartmentEditLog);
        $this->assertNotNull($createdTenderDepartmentEditLog['id'], 'Created TenderDepartmentEditLog must have id specified');
        $this->assertNotNull(TenderDepartmentEditLog::find($createdTenderDepartmentEditLog['id']), 'TenderDepartmentEditLog with given id must be in DB');
        $this->assertModelData($tenderDepartmentEditLog, $createdTenderDepartmentEditLog);
    }

    /**
     * @test read
     */
    public function test_read_tender_department_edit_log()
    {
        $tenderDepartmentEditLog = factory(TenderDepartmentEditLog::class)->create();

        $dbTenderDepartmentEditLog = $this->tenderDepartmentEditLogRepo->find($tenderDepartmentEditLog->id);

        $dbTenderDepartmentEditLog = $dbTenderDepartmentEditLog->toArray();
        $this->assertModelData($tenderDepartmentEditLog->toArray(), $dbTenderDepartmentEditLog);
    }

    /**
     * @test update
     */
    public function test_update_tender_department_edit_log()
    {
        $tenderDepartmentEditLog = factory(TenderDepartmentEditLog::class)->create();
        $fakeTenderDepartmentEditLog = factory(TenderDepartmentEditLog::class)->make()->toArray();

        $updatedTenderDepartmentEditLog = $this->tenderDepartmentEditLogRepo->update($fakeTenderDepartmentEditLog, $tenderDepartmentEditLog->id);

        $this->assertModelData($fakeTenderDepartmentEditLog, $updatedTenderDepartmentEditLog->toArray());
        $dbTenderDepartmentEditLog = $this->tenderDepartmentEditLogRepo->find($tenderDepartmentEditLog->id);
        $this->assertModelData($fakeTenderDepartmentEditLog, $dbTenderDepartmentEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_department_edit_log()
    {
        $tenderDepartmentEditLog = factory(TenderDepartmentEditLog::class)->create();

        $resp = $this->tenderDepartmentEditLogRepo->delete($tenderDepartmentEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderDepartmentEditLog::find($tenderDepartmentEditLog->id), 'TenderDepartmentEditLog should not exist in DB');
    }
}
