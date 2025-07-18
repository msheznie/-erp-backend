<?php namespace Tests\Repositories;

use App\Models\SrmTenderMasterEditLog;
use App\Repositories\SrmTenderMasterEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SrmTenderMasterEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SrmTenderMasterEditLogRepository
     */
    protected $srmTenderMasterEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->srmTenderMasterEditLogRepo = \App::make(SrmTenderMasterEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_srm_tender_master_edit_log()
    {
        $srmTenderMasterEditLog = factory(SrmTenderMasterEditLog::class)->make()->toArray();

        $createdSrmTenderMasterEditLog = $this->srmTenderMasterEditLogRepo->create($srmTenderMasterEditLog);

        $createdSrmTenderMasterEditLog = $createdSrmTenderMasterEditLog->toArray();
        $this->assertArrayHasKey('id', $createdSrmTenderMasterEditLog);
        $this->assertNotNull($createdSrmTenderMasterEditLog['id'], 'Created SrmTenderMasterEditLog must have id specified');
        $this->assertNotNull(SrmTenderMasterEditLog::find($createdSrmTenderMasterEditLog['id']), 'SrmTenderMasterEditLog with given id must be in DB');
        $this->assertModelData($srmTenderMasterEditLog, $createdSrmTenderMasterEditLog);
    }

    /**
     * @test read
     */
    public function test_read_srm_tender_master_edit_log()
    {
        $srmTenderMasterEditLog = factory(SrmTenderMasterEditLog::class)->create();

        $dbSrmTenderMasterEditLog = $this->srmTenderMasterEditLogRepo->find($srmTenderMasterEditLog->id);

        $dbSrmTenderMasterEditLog = $dbSrmTenderMasterEditLog->toArray();
        $this->assertModelData($srmTenderMasterEditLog->toArray(), $dbSrmTenderMasterEditLog);
    }

    /**
     * @test update
     */
    public function test_update_srm_tender_master_edit_log()
    {
        $srmTenderMasterEditLog = factory(SrmTenderMasterEditLog::class)->create();
        $fakeSrmTenderMasterEditLog = factory(SrmTenderMasterEditLog::class)->make()->toArray();

        $updatedSrmTenderMasterEditLog = $this->srmTenderMasterEditLogRepo->update($fakeSrmTenderMasterEditLog, $srmTenderMasterEditLog->id);

        $this->assertModelData($fakeSrmTenderMasterEditLog, $updatedSrmTenderMasterEditLog->toArray());
        $dbSrmTenderMasterEditLog = $this->srmTenderMasterEditLogRepo->find($srmTenderMasterEditLog->id);
        $this->assertModelData($fakeSrmTenderMasterEditLog, $dbSrmTenderMasterEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_srm_tender_master_edit_log()
    {
        $srmTenderMasterEditLog = factory(SrmTenderMasterEditLog::class)->create();

        $resp = $this->srmTenderMasterEditLogRepo->delete($srmTenderMasterEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(SrmTenderMasterEditLog::find($srmTenderMasterEditLog->id), 'SrmTenderMasterEditLog should not exist in DB');
    }
}
