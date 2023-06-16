<?php namespace Tests\Repositories;

use App\Models\SrmTenderBidEmployeeDetailsEditLog;
use App\Repositories\SrmTenderBidEmployeeDetailsEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SrmTenderBidEmployeeDetailsEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SrmTenderBidEmployeeDetailsEditLogRepository
     */
    protected $srmTenderBidEmployeeDetailsEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->srmTenderBidEmployeeDetailsEditLogRepo = \App::make(SrmTenderBidEmployeeDetailsEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_srm_tender_bid_employee_details_edit_log()
    {
        $srmTenderBidEmployeeDetailsEditLog = factory(SrmTenderBidEmployeeDetailsEditLog::class)->make()->toArray();

        $createdSrmTenderBidEmployeeDetailsEditLog = $this->srmTenderBidEmployeeDetailsEditLogRepo->create($srmTenderBidEmployeeDetailsEditLog);

        $createdSrmTenderBidEmployeeDetailsEditLog = $createdSrmTenderBidEmployeeDetailsEditLog->toArray();
        $this->assertArrayHasKey('id', $createdSrmTenderBidEmployeeDetailsEditLog);
        $this->assertNotNull($createdSrmTenderBidEmployeeDetailsEditLog['id'], 'Created SrmTenderBidEmployeeDetailsEditLog must have id specified');
        $this->assertNotNull(SrmTenderBidEmployeeDetailsEditLog::find($createdSrmTenderBidEmployeeDetailsEditLog['id']), 'SrmTenderBidEmployeeDetailsEditLog with given id must be in DB');
        $this->assertModelData($srmTenderBidEmployeeDetailsEditLog, $createdSrmTenderBidEmployeeDetailsEditLog);
    }

    /**
     * @test read
     */
    public function test_read_srm_tender_bid_employee_details_edit_log()
    {
        $srmTenderBidEmployeeDetailsEditLog = factory(SrmTenderBidEmployeeDetailsEditLog::class)->create();

        $dbSrmTenderBidEmployeeDetailsEditLog = $this->srmTenderBidEmployeeDetailsEditLogRepo->find($srmTenderBidEmployeeDetailsEditLog->id);

        $dbSrmTenderBidEmployeeDetailsEditLog = $dbSrmTenderBidEmployeeDetailsEditLog->toArray();
        $this->assertModelData($srmTenderBidEmployeeDetailsEditLog->toArray(), $dbSrmTenderBidEmployeeDetailsEditLog);
    }

    /**
     * @test update
     */
    public function test_update_srm_tender_bid_employee_details_edit_log()
    {
        $srmTenderBidEmployeeDetailsEditLog = factory(SrmTenderBidEmployeeDetailsEditLog::class)->create();
        $fakeSrmTenderBidEmployeeDetailsEditLog = factory(SrmTenderBidEmployeeDetailsEditLog::class)->make()->toArray();

        $updatedSrmTenderBidEmployeeDetailsEditLog = $this->srmTenderBidEmployeeDetailsEditLogRepo->update($fakeSrmTenderBidEmployeeDetailsEditLog, $srmTenderBidEmployeeDetailsEditLog->id);

        $this->assertModelData($fakeSrmTenderBidEmployeeDetailsEditLog, $updatedSrmTenderBidEmployeeDetailsEditLog->toArray());
        $dbSrmTenderBidEmployeeDetailsEditLog = $this->srmTenderBidEmployeeDetailsEditLogRepo->find($srmTenderBidEmployeeDetailsEditLog->id);
        $this->assertModelData($fakeSrmTenderBidEmployeeDetailsEditLog, $dbSrmTenderBidEmployeeDetailsEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_srm_tender_bid_employee_details_edit_log()
    {
        $srmTenderBidEmployeeDetailsEditLog = factory(SrmTenderBidEmployeeDetailsEditLog::class)->create();

        $resp = $this->srmTenderBidEmployeeDetailsEditLogRepo->delete($srmTenderBidEmployeeDetailsEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(SrmTenderBidEmployeeDetailsEditLog::find($srmTenderBidEmployeeDetailsEditLog->id), 'SrmTenderBidEmployeeDetailsEditLog should not exist in DB');
    }
}
