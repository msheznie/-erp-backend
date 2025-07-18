<?php namespace Tests\Repositories;

use App\Models\TenderPurchaseRequestEditLog;
use App\Repositories\TenderPurchaseRequestEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderPurchaseRequestEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderPurchaseRequestEditLogRepository
     */
    protected $tenderPurchaseRequestEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderPurchaseRequestEditLogRepo = \App::make(TenderPurchaseRequestEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_purchase_request_edit_log()
    {
        $tenderPurchaseRequestEditLog = factory(TenderPurchaseRequestEditLog::class)->make()->toArray();

        $createdTenderPurchaseRequestEditLog = $this->tenderPurchaseRequestEditLogRepo->create($tenderPurchaseRequestEditLog);

        $createdTenderPurchaseRequestEditLog = $createdTenderPurchaseRequestEditLog->toArray();
        $this->assertArrayHasKey('id', $createdTenderPurchaseRequestEditLog);
        $this->assertNotNull($createdTenderPurchaseRequestEditLog['id'], 'Created TenderPurchaseRequestEditLog must have id specified');
        $this->assertNotNull(TenderPurchaseRequestEditLog::find($createdTenderPurchaseRequestEditLog['id']), 'TenderPurchaseRequestEditLog with given id must be in DB');
        $this->assertModelData($tenderPurchaseRequestEditLog, $createdTenderPurchaseRequestEditLog);
    }

    /**
     * @test read
     */
    public function test_read_tender_purchase_request_edit_log()
    {
        $tenderPurchaseRequestEditLog = factory(TenderPurchaseRequestEditLog::class)->create();

        $dbTenderPurchaseRequestEditLog = $this->tenderPurchaseRequestEditLogRepo->find($tenderPurchaseRequestEditLog->id);

        $dbTenderPurchaseRequestEditLog = $dbTenderPurchaseRequestEditLog->toArray();
        $this->assertModelData($tenderPurchaseRequestEditLog->toArray(), $dbTenderPurchaseRequestEditLog);
    }

    /**
     * @test update
     */
    public function test_update_tender_purchase_request_edit_log()
    {
        $tenderPurchaseRequestEditLog = factory(TenderPurchaseRequestEditLog::class)->create();
        $fakeTenderPurchaseRequestEditLog = factory(TenderPurchaseRequestEditLog::class)->make()->toArray();

        $updatedTenderPurchaseRequestEditLog = $this->tenderPurchaseRequestEditLogRepo->update($fakeTenderPurchaseRequestEditLog, $tenderPurchaseRequestEditLog->id);

        $this->assertModelData($fakeTenderPurchaseRequestEditLog, $updatedTenderPurchaseRequestEditLog->toArray());
        $dbTenderPurchaseRequestEditLog = $this->tenderPurchaseRequestEditLogRepo->find($tenderPurchaseRequestEditLog->id);
        $this->assertModelData($fakeTenderPurchaseRequestEditLog, $dbTenderPurchaseRequestEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_purchase_request_edit_log()
    {
        $tenderPurchaseRequestEditLog = factory(TenderPurchaseRequestEditLog::class)->create();

        $resp = $this->tenderPurchaseRequestEditLogRepo->delete($tenderPurchaseRequestEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderPurchaseRequestEditLog::find($tenderPurchaseRequestEditLog->id), 'TenderPurchaseRequestEditLog should not exist in DB');
    }
}
