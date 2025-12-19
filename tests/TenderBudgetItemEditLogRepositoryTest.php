<?php namespace Tests\Repositories;

use App\Models\TenderBudgetItemEditLog;
use App\Repositories\TenderBudgetItemEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderBudgetItemEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderBudgetItemEditLogRepository
     */
    protected $tenderBudgetItemEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderBudgetItemEditLogRepo = \App::make(TenderBudgetItemEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_budget_item_edit_log()
    {
        $tenderBudgetItemEditLog = factory(TenderBudgetItemEditLog::class)->make()->toArray();

        $createdTenderBudgetItemEditLog = $this->tenderBudgetItemEditLogRepo->create($tenderBudgetItemEditLog);

        $createdTenderBudgetItemEditLog = $createdTenderBudgetItemEditLog->toArray();
        $this->assertArrayHasKey('id', $createdTenderBudgetItemEditLog);
        $this->assertNotNull($createdTenderBudgetItemEditLog['id'], 'Created TenderBudgetItemEditLog must have id specified');
        $this->assertNotNull(TenderBudgetItemEditLog::find($createdTenderBudgetItemEditLog['id']), 'TenderBudgetItemEditLog with given id must be in DB');
        $this->assertModelData($tenderBudgetItemEditLog, $createdTenderBudgetItemEditLog);
    }

    /**
     * @test read
     */
    public function test_read_tender_budget_item_edit_log()
    {
        $tenderBudgetItemEditLog = factory(TenderBudgetItemEditLog::class)->create();

        $dbTenderBudgetItemEditLog = $this->tenderBudgetItemEditLogRepo->find($tenderBudgetItemEditLog->id);

        $dbTenderBudgetItemEditLog = $dbTenderBudgetItemEditLog->toArray();
        $this->assertModelData($tenderBudgetItemEditLog->toArray(), $dbTenderBudgetItemEditLog);
    }

    /**
     * @test update
     */
    public function test_update_tender_budget_item_edit_log()
    {
        $tenderBudgetItemEditLog = factory(TenderBudgetItemEditLog::class)->create();
        $fakeTenderBudgetItemEditLog = factory(TenderBudgetItemEditLog::class)->make()->toArray();

        $updatedTenderBudgetItemEditLog = $this->tenderBudgetItemEditLogRepo->update($fakeTenderBudgetItemEditLog, $tenderBudgetItemEditLog->id);

        $this->assertModelData($fakeTenderBudgetItemEditLog, $updatedTenderBudgetItemEditLog->toArray());
        $dbTenderBudgetItemEditLog = $this->tenderBudgetItemEditLogRepo->find($tenderBudgetItemEditLog->id);
        $this->assertModelData($fakeTenderBudgetItemEditLog, $dbTenderBudgetItemEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_budget_item_edit_log()
    {
        $tenderBudgetItemEditLog = factory(TenderBudgetItemEditLog::class)->create();

        $resp = $this->tenderBudgetItemEditLogRepo->delete($tenderBudgetItemEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderBudgetItemEditLog::find($tenderBudgetItemEditLog->id), 'TenderBudgetItemEditLog should not exist in DB');
    }
}
