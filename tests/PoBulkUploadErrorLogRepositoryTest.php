<?php namespace Tests\Repositories;

use App\Models\PoBulkUploadErrorLog;
use App\Repositories\PoBulkUploadErrorLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PoBulkUploadErrorLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PoBulkUploadErrorLogRepository
     */
    protected $poBulkUploadErrorLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->poBulkUploadErrorLogRepo = \App::make(PoBulkUploadErrorLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_po_bulk_upload_error_log()
    {
        $poBulkUploadErrorLog = factory(PoBulkUploadErrorLog::class)->make()->toArray();

        $createdPoBulkUploadErrorLog = $this->poBulkUploadErrorLogRepo->create($poBulkUploadErrorLog);

        $createdPoBulkUploadErrorLog = $createdPoBulkUploadErrorLog->toArray();
        $this->assertArrayHasKey('id', $createdPoBulkUploadErrorLog);
        $this->assertNotNull($createdPoBulkUploadErrorLog['id'], 'Created PoBulkUploadErrorLog must have id specified');
        $this->assertNotNull(PoBulkUploadErrorLog::find($createdPoBulkUploadErrorLog['id']), 'PoBulkUploadErrorLog with given id must be in DB');
        $this->assertModelData($poBulkUploadErrorLog, $createdPoBulkUploadErrorLog);
    }

    /**
     * @test read
     */
    public function test_read_po_bulk_upload_error_log()
    {
        $poBulkUploadErrorLog = factory(PoBulkUploadErrorLog::class)->create();

        $dbPoBulkUploadErrorLog = $this->poBulkUploadErrorLogRepo->find($poBulkUploadErrorLog->id);

        $dbPoBulkUploadErrorLog = $dbPoBulkUploadErrorLog->toArray();
        $this->assertModelData($poBulkUploadErrorLog->toArray(), $dbPoBulkUploadErrorLog);
    }

    /**
     * @test update
     */
    public function test_update_po_bulk_upload_error_log()
    {
        $poBulkUploadErrorLog = factory(PoBulkUploadErrorLog::class)->create();
        $fakePoBulkUploadErrorLog = factory(PoBulkUploadErrorLog::class)->make()->toArray();

        $updatedPoBulkUploadErrorLog = $this->poBulkUploadErrorLogRepo->update($fakePoBulkUploadErrorLog, $poBulkUploadErrorLog->id);

        $this->assertModelData($fakePoBulkUploadErrorLog, $updatedPoBulkUploadErrorLog->toArray());
        $dbPoBulkUploadErrorLog = $this->poBulkUploadErrorLogRepo->find($poBulkUploadErrorLog->id);
        $this->assertModelData($fakePoBulkUploadErrorLog, $dbPoBulkUploadErrorLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_po_bulk_upload_error_log()
    {
        $poBulkUploadErrorLog = factory(PoBulkUploadErrorLog::class)->create();

        $resp = $this->poBulkUploadErrorLogRepo->delete($poBulkUploadErrorLog->id);

        $this->assertTrue($resp);
        $this->assertNull(PoBulkUploadErrorLog::find($poBulkUploadErrorLog->id), 'PoBulkUploadErrorLog should not exist in DB');
    }
}
