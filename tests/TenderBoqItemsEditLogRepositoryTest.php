<?php namespace Tests\Repositories;

use App\Models\TenderBoqItemsEditLog;
use App\Repositories\TenderBoqItemsEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderBoqItemsEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderBoqItemsEditLogRepository
     */
    protected $tenderBoqItemsEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderBoqItemsEditLogRepo = \App::make(TenderBoqItemsEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_boq_items_edit_log()
    {
        $tenderBoqItemsEditLog = factory(TenderBoqItemsEditLog::class)->make()->toArray();

        $createdTenderBoqItemsEditLog = $this->tenderBoqItemsEditLogRepo->create($tenderBoqItemsEditLog);

        $createdTenderBoqItemsEditLog = $createdTenderBoqItemsEditLog->toArray();
        $this->assertArrayHasKey('id', $createdTenderBoqItemsEditLog);
        $this->assertNotNull($createdTenderBoqItemsEditLog['id'], 'Created TenderBoqItemsEditLog must have id specified');
        $this->assertNotNull(TenderBoqItemsEditLog::find($createdTenderBoqItemsEditLog['id']), 'TenderBoqItemsEditLog with given id must be in DB');
        $this->assertModelData($tenderBoqItemsEditLog, $createdTenderBoqItemsEditLog);
    }

    /**
     * @test read
     */
    public function test_read_tender_boq_items_edit_log()
    {
        $tenderBoqItemsEditLog = factory(TenderBoqItemsEditLog::class)->create();

        $dbTenderBoqItemsEditLog = $this->tenderBoqItemsEditLogRepo->find($tenderBoqItemsEditLog->id);

        $dbTenderBoqItemsEditLog = $dbTenderBoqItemsEditLog->toArray();
        $this->assertModelData($tenderBoqItemsEditLog->toArray(), $dbTenderBoqItemsEditLog);
    }

    /**
     * @test update
     */
    public function test_update_tender_boq_items_edit_log()
    {
        $tenderBoqItemsEditLog = factory(TenderBoqItemsEditLog::class)->create();
        $fakeTenderBoqItemsEditLog = factory(TenderBoqItemsEditLog::class)->make()->toArray();

        $updatedTenderBoqItemsEditLog = $this->tenderBoqItemsEditLogRepo->update($fakeTenderBoqItemsEditLog, $tenderBoqItemsEditLog->id);

        $this->assertModelData($fakeTenderBoqItemsEditLog, $updatedTenderBoqItemsEditLog->toArray());
        $dbTenderBoqItemsEditLog = $this->tenderBoqItemsEditLogRepo->find($tenderBoqItemsEditLog->id);
        $this->assertModelData($fakeTenderBoqItemsEditLog, $dbTenderBoqItemsEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_boq_items_edit_log()
    {
        $tenderBoqItemsEditLog = factory(TenderBoqItemsEditLog::class)->create();

        $resp = $this->tenderBoqItemsEditLogRepo->delete($tenderBoqItemsEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderBoqItemsEditLog::find($tenderBoqItemsEditLog->id), 'TenderBoqItemsEditLog should not exist in DB');
    }
}
