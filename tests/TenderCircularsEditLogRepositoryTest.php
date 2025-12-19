<?php namespace Tests\Repositories;

use App\Models\TenderCircularsEditLog;
use App\Repositories\TenderCircularsEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderCircularsEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderCircularsEditLogRepository
     */
    protected $tenderCircularsEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderCircularsEditLogRepo = \App::make(TenderCircularsEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_circulars_edit_log()
    {
        $tenderCircularsEditLog = factory(TenderCircularsEditLog::class)->make()->toArray();

        $createdTenderCircularsEditLog = $this->tenderCircularsEditLogRepo->create($tenderCircularsEditLog);

        $createdTenderCircularsEditLog = $createdTenderCircularsEditLog->toArray();
        $this->assertArrayHasKey('id', $createdTenderCircularsEditLog);
        $this->assertNotNull($createdTenderCircularsEditLog['id'], 'Created TenderCircularsEditLog must have id specified');
        $this->assertNotNull(TenderCircularsEditLog::find($createdTenderCircularsEditLog['id']), 'TenderCircularsEditLog with given id must be in DB');
        $this->assertModelData($tenderCircularsEditLog, $createdTenderCircularsEditLog);
    }

    /**
     * @test read
     */
    public function test_read_tender_circulars_edit_log()
    {
        $tenderCircularsEditLog = factory(TenderCircularsEditLog::class)->create();

        $dbTenderCircularsEditLog = $this->tenderCircularsEditLogRepo->find($tenderCircularsEditLog->id);

        $dbTenderCircularsEditLog = $dbTenderCircularsEditLog->toArray();
        $this->assertModelData($tenderCircularsEditLog->toArray(), $dbTenderCircularsEditLog);
    }

    /**
     * @test update
     */
    public function test_update_tender_circulars_edit_log()
    {
        $tenderCircularsEditLog = factory(TenderCircularsEditLog::class)->create();
        $fakeTenderCircularsEditLog = factory(TenderCircularsEditLog::class)->make()->toArray();

        $updatedTenderCircularsEditLog = $this->tenderCircularsEditLogRepo->update($fakeTenderCircularsEditLog, $tenderCircularsEditLog->id);

        $this->assertModelData($fakeTenderCircularsEditLog, $updatedTenderCircularsEditLog->toArray());
        $dbTenderCircularsEditLog = $this->tenderCircularsEditLogRepo->find($tenderCircularsEditLog->id);
        $this->assertModelData($fakeTenderCircularsEditLog, $dbTenderCircularsEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_circulars_edit_log()
    {
        $tenderCircularsEditLog = factory(TenderCircularsEditLog::class)->create();

        $resp = $this->tenderCircularsEditLogRepo->delete($tenderCircularsEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderCircularsEditLog::find($tenderCircularsEditLog->id), 'TenderCircularsEditLog should not exist in DB');
    }
}
