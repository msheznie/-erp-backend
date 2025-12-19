<?php namespace Tests\Repositories;

use App\Models\TenderSiteVisitDateEditLog;
use App\Repositories\TenderSiteVisitDateEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderSiteVisitDateEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderSiteVisitDateEditLogRepository
     */
    protected $tenderSiteVisitDateEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderSiteVisitDateEditLogRepo = \App::make(TenderSiteVisitDateEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_site_visit_date_edit_log()
    {
        $tenderSiteVisitDateEditLog = factory(TenderSiteVisitDateEditLog::class)->make()->toArray();

        $createdTenderSiteVisitDateEditLog = $this->tenderSiteVisitDateEditLogRepo->create($tenderSiteVisitDateEditLog);

        $createdTenderSiteVisitDateEditLog = $createdTenderSiteVisitDateEditLog->toArray();
        $this->assertArrayHasKey('id', $createdTenderSiteVisitDateEditLog);
        $this->assertNotNull($createdTenderSiteVisitDateEditLog['id'], 'Created TenderSiteVisitDateEditLog must have id specified');
        $this->assertNotNull(TenderSiteVisitDateEditLog::find($createdTenderSiteVisitDateEditLog['id']), 'TenderSiteVisitDateEditLog with given id must be in DB');
        $this->assertModelData($tenderSiteVisitDateEditLog, $createdTenderSiteVisitDateEditLog);
    }

    /**
     * @test read
     */
    public function test_read_tender_site_visit_date_edit_log()
    {
        $tenderSiteVisitDateEditLog = factory(TenderSiteVisitDateEditLog::class)->create();

        $dbTenderSiteVisitDateEditLog = $this->tenderSiteVisitDateEditLogRepo->find($tenderSiteVisitDateEditLog->id);

        $dbTenderSiteVisitDateEditLog = $dbTenderSiteVisitDateEditLog->toArray();
        $this->assertModelData($tenderSiteVisitDateEditLog->toArray(), $dbTenderSiteVisitDateEditLog);
    }

    /**
     * @test update
     */
    public function test_update_tender_site_visit_date_edit_log()
    {
        $tenderSiteVisitDateEditLog = factory(TenderSiteVisitDateEditLog::class)->create();
        $fakeTenderSiteVisitDateEditLog = factory(TenderSiteVisitDateEditLog::class)->make()->toArray();

        $updatedTenderSiteVisitDateEditLog = $this->tenderSiteVisitDateEditLogRepo->update($fakeTenderSiteVisitDateEditLog, $tenderSiteVisitDateEditLog->id);

        $this->assertModelData($fakeTenderSiteVisitDateEditLog, $updatedTenderSiteVisitDateEditLog->toArray());
        $dbTenderSiteVisitDateEditLog = $this->tenderSiteVisitDateEditLogRepo->find($tenderSiteVisitDateEditLog->id);
        $this->assertModelData($fakeTenderSiteVisitDateEditLog, $dbTenderSiteVisitDateEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_site_visit_date_edit_log()
    {
        $tenderSiteVisitDateEditLog = factory(TenderSiteVisitDateEditLog::class)->create();

        $resp = $this->tenderSiteVisitDateEditLogRepo->delete($tenderSiteVisitDateEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderSiteVisitDateEditLog::find($tenderSiteVisitDateEditLog->id), 'TenderSiteVisitDateEditLog should not exist in DB');
    }
}
