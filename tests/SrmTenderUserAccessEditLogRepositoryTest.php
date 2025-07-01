<?php namespace Tests\Repositories;

use App\Models\SrmTenderUserAccessEditLog;
use App\Repositories\SrmTenderUserAccessEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SrmTenderUserAccessEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SrmTenderUserAccessEditLogRepository
     */
    protected $srmTenderUserAccessEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->srmTenderUserAccessEditLogRepo = \App::make(SrmTenderUserAccessEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_srm_tender_user_access_edit_log()
    {
        $srmTenderUserAccessEditLog = factory(SrmTenderUserAccessEditLog::class)->make()->toArray();

        $createdSrmTenderUserAccessEditLog = $this->srmTenderUserAccessEditLogRepo->create($srmTenderUserAccessEditLog);

        $createdSrmTenderUserAccessEditLog = $createdSrmTenderUserAccessEditLog->toArray();
        $this->assertArrayHasKey('id', $createdSrmTenderUserAccessEditLog);
        $this->assertNotNull($createdSrmTenderUserAccessEditLog['id'], 'Created SrmTenderUserAccessEditLog must have id specified');
        $this->assertNotNull(SrmTenderUserAccessEditLog::find($createdSrmTenderUserAccessEditLog['id']), 'SrmTenderUserAccessEditLog with given id must be in DB');
        $this->assertModelData($srmTenderUserAccessEditLog, $createdSrmTenderUserAccessEditLog);
    }

    /**
     * @test read
     */
    public function test_read_srm_tender_user_access_edit_log()
    {
        $srmTenderUserAccessEditLog = factory(SrmTenderUserAccessEditLog::class)->create();

        $dbSrmTenderUserAccessEditLog = $this->srmTenderUserAccessEditLogRepo->find($srmTenderUserAccessEditLog->id);

        $dbSrmTenderUserAccessEditLog = $dbSrmTenderUserAccessEditLog->toArray();
        $this->assertModelData($srmTenderUserAccessEditLog->toArray(), $dbSrmTenderUserAccessEditLog);
    }

    /**
     * @test update
     */
    public function test_update_srm_tender_user_access_edit_log()
    {
        $srmTenderUserAccessEditLog = factory(SrmTenderUserAccessEditLog::class)->create();
        $fakeSrmTenderUserAccessEditLog = factory(SrmTenderUserAccessEditLog::class)->make()->toArray();

        $updatedSrmTenderUserAccessEditLog = $this->srmTenderUserAccessEditLogRepo->update($fakeSrmTenderUserAccessEditLog, $srmTenderUserAccessEditLog->id);

        $this->assertModelData($fakeSrmTenderUserAccessEditLog, $updatedSrmTenderUserAccessEditLog->toArray());
        $dbSrmTenderUserAccessEditLog = $this->srmTenderUserAccessEditLogRepo->find($srmTenderUserAccessEditLog->id);
        $this->assertModelData($fakeSrmTenderUserAccessEditLog, $dbSrmTenderUserAccessEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_srm_tender_user_access_edit_log()
    {
        $srmTenderUserAccessEditLog = factory(SrmTenderUserAccessEditLog::class)->create();

        $resp = $this->srmTenderUserAccessEditLogRepo->delete($srmTenderUserAccessEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(SrmTenderUserAccessEditLog::find($srmTenderUserAccessEditLog->id), 'SrmTenderUserAccessEditLog should not exist in DB');
    }
}
