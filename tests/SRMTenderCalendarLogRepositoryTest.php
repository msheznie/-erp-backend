<?php namespace Tests\Repositories;

use App\Models\SRMTenderCalendarLog;
use App\Repositories\SRMTenderCalendarLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SRMTenderCalendarLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SRMTenderCalendarLogRepository
     */
    protected $sRMTenderCalendarLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sRMTenderCalendarLogRepo = \App::make(SRMTenderCalendarLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_r_m_tender_calendar_log()
    {
        $sRMTenderCalendarLog = factory(SRMTenderCalendarLog::class)->make()->toArray();

        $createdSRMTenderCalendarLog = $this->sRMTenderCalendarLogRepo->create($sRMTenderCalendarLog);

        $createdSRMTenderCalendarLog = $createdSRMTenderCalendarLog->toArray();
        $this->assertArrayHasKey('id', $createdSRMTenderCalendarLog);
        $this->assertNotNull($createdSRMTenderCalendarLog['id'], 'Created SRMTenderCalendarLog must have id specified');
        $this->assertNotNull(SRMTenderCalendarLog::find($createdSRMTenderCalendarLog['id']), 'SRMTenderCalendarLog with given id must be in DB');
        $this->assertModelData($sRMTenderCalendarLog, $createdSRMTenderCalendarLog);
    }

    /**
     * @test read
     */
    public function test_read_s_r_m_tender_calendar_log()
    {
        $sRMTenderCalendarLog = factory(SRMTenderCalendarLog::class)->create();

        $dbSRMTenderCalendarLog = $this->sRMTenderCalendarLogRepo->find($sRMTenderCalendarLog->id);

        $dbSRMTenderCalendarLog = $dbSRMTenderCalendarLog->toArray();
        $this->assertModelData($sRMTenderCalendarLog->toArray(), $dbSRMTenderCalendarLog);
    }

    /**
     * @test update
     */
    public function test_update_s_r_m_tender_calendar_log()
    {
        $sRMTenderCalendarLog = factory(SRMTenderCalendarLog::class)->create();
        $fakeSRMTenderCalendarLog = factory(SRMTenderCalendarLog::class)->make()->toArray();

        $updatedSRMTenderCalendarLog = $this->sRMTenderCalendarLogRepo->update($fakeSRMTenderCalendarLog, $sRMTenderCalendarLog->id);

        $this->assertModelData($fakeSRMTenderCalendarLog, $updatedSRMTenderCalendarLog->toArray());
        $dbSRMTenderCalendarLog = $this->sRMTenderCalendarLogRepo->find($sRMTenderCalendarLog->id);
        $this->assertModelData($fakeSRMTenderCalendarLog, $dbSRMTenderCalendarLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_r_m_tender_calendar_log()
    {
        $sRMTenderCalendarLog = factory(SRMTenderCalendarLog::class)->create();

        $resp = $this->sRMTenderCalendarLogRepo->delete($sRMTenderCalendarLog->id);

        $this->assertTrue($resp);
        $this->assertNull(SRMTenderCalendarLog::find($sRMTenderCalendarLog->id), 'SRMTenderCalendarLog should not exist in DB');
    }
}
