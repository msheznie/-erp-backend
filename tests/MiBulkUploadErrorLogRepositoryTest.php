<?php namespace Tests\Repositories;

use App\Models\MiBulkUploadErrorLog;
use App\Repositories\MiBulkUploadErrorLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class MiBulkUploadErrorLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var MiBulkUploadErrorLogRepository
     */
    protected $miBulkUploadErrorLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->miBulkUploadErrorLogRepo = \App::make(MiBulkUploadErrorLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_mi_bulk_upload_error_log()
    {
        $miBulkUploadErrorLog = factory(MiBulkUploadErrorLog::class)->make()->toArray();

        $createdMiBulkUploadErrorLog = $this->miBulkUploadErrorLogRepo->create($miBulkUploadErrorLog);

        $createdMiBulkUploadErrorLog = $createdMiBulkUploadErrorLog->toArray();
        $this->assertArrayHasKey('id', $createdMiBulkUploadErrorLog);
        $this->assertNotNull($createdMiBulkUploadErrorLog['id'], 'Created MiBulkUploadErrorLog must have id specified');
        $this->assertNotNull(MiBulkUploadErrorLog::find($createdMiBulkUploadErrorLog['id']), 'MiBulkUploadErrorLog with given id must be in DB');
        $this->assertModelData($miBulkUploadErrorLog, $createdMiBulkUploadErrorLog);
    }

    /**
     * @test read
     */
    public function test_read_mi_bulk_upload_error_log()
    {
        $miBulkUploadErrorLog = factory(MiBulkUploadErrorLog::class)->create();

        $dbMiBulkUploadErrorLog = $this->miBulkUploadErrorLogRepo->find($miBulkUploadErrorLog->id);

        $dbMiBulkUploadErrorLog = $dbMiBulkUploadErrorLog->toArray();
        $this->assertModelData($miBulkUploadErrorLog->toArray(), $dbMiBulkUploadErrorLog);
    }

    /**
     * @test update
     */
    public function test_update_mi_bulk_upload_error_log()
    {
        $miBulkUploadErrorLog = factory(MiBulkUploadErrorLog::class)->create();
        $fakeMiBulkUploadErrorLog = factory(MiBulkUploadErrorLog::class)->make()->toArray();

        $updatedMiBulkUploadErrorLog = $this->miBulkUploadErrorLogRepo->update($fakeMiBulkUploadErrorLog, $miBulkUploadErrorLog->id);

        $this->assertModelData($fakeMiBulkUploadErrorLog, $updatedMiBulkUploadErrorLog->toArray());
        $dbMiBulkUploadErrorLog = $this->miBulkUploadErrorLogRepo->find($miBulkUploadErrorLog->id);
        $this->assertModelData($fakeMiBulkUploadErrorLog, $dbMiBulkUploadErrorLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_mi_bulk_upload_error_log()
    {
        $miBulkUploadErrorLog = factory(MiBulkUploadErrorLog::class)->create();

        $resp = $this->miBulkUploadErrorLogRepo->delete($miBulkUploadErrorLog->id);

        $this->assertTrue($resp);
        $this->assertNull(MiBulkUploadErrorLog::find($miBulkUploadErrorLog->id), 'MiBulkUploadErrorLog should not exist in DB');
    }
}
