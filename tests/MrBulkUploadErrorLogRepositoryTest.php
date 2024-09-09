<?php namespace Tests\Repositories;

use App\Models\MrBulkUploadErrorLog;
use App\Repositories\MrBulkUploadErrorLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class MrBulkUploadErrorLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var MrBulkUploadErrorLogRepository
     */
    protected $mrBulkUploadErrorLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->mrBulkUploadErrorLogRepo = \App::make(MrBulkUploadErrorLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_mr_bulk_upload_error_log()
    {
        $mrBulkUploadErrorLog = factory(MrBulkUploadErrorLog::class)->make()->toArray();

        $createdMrBulkUploadErrorLog = $this->mrBulkUploadErrorLogRepo->create($mrBulkUploadErrorLog);

        $createdMrBulkUploadErrorLog = $createdMrBulkUploadErrorLog->toArray();
        $this->assertArrayHasKey('id', $createdMrBulkUploadErrorLog);
        $this->assertNotNull($createdMrBulkUploadErrorLog['id'], 'Created MrBulkUploadErrorLog must have id specified');
        $this->assertNotNull(MrBulkUploadErrorLog::find($createdMrBulkUploadErrorLog['id']), 'MrBulkUploadErrorLog with given id must be in DB');
        $this->assertModelData($mrBulkUploadErrorLog, $createdMrBulkUploadErrorLog);
    }

    /**
     * @test read
     */
    public function test_read_mr_bulk_upload_error_log()
    {
        $mrBulkUploadErrorLog = factory(MrBulkUploadErrorLog::class)->create();

        $dbMrBulkUploadErrorLog = $this->mrBulkUploadErrorLogRepo->find($mrBulkUploadErrorLog->id);

        $dbMrBulkUploadErrorLog = $dbMrBulkUploadErrorLog->toArray();
        $this->assertModelData($mrBulkUploadErrorLog->toArray(), $dbMrBulkUploadErrorLog);
    }

    /**
     * @test update
     */
    public function test_update_mr_bulk_upload_error_log()
    {
        $mrBulkUploadErrorLog = factory(MrBulkUploadErrorLog::class)->create();
        $fakeMrBulkUploadErrorLog = factory(MrBulkUploadErrorLog::class)->make()->toArray();

        $updatedMrBulkUploadErrorLog = $this->mrBulkUploadErrorLogRepo->update($fakeMrBulkUploadErrorLog, $mrBulkUploadErrorLog->id);

        $this->assertModelData($fakeMrBulkUploadErrorLog, $updatedMrBulkUploadErrorLog->toArray());
        $dbMrBulkUploadErrorLog = $this->mrBulkUploadErrorLogRepo->find($mrBulkUploadErrorLog->id);
        $this->assertModelData($fakeMrBulkUploadErrorLog, $dbMrBulkUploadErrorLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_mr_bulk_upload_error_log()
    {
        $mrBulkUploadErrorLog = factory(MrBulkUploadErrorLog::class)->create();

        $resp = $this->mrBulkUploadErrorLogRepo->delete($mrBulkUploadErrorLog->id);

        $this->assertTrue($resp);
        $this->assertNull(MrBulkUploadErrorLog::find($mrBulkUploadErrorLog->id), 'MrBulkUploadErrorLog should not exist in DB');
    }
}
