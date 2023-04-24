<?php namespace Tests\Repositories;

use App\Models\ProcumentActivityEditLog;
use App\Repositories\ProcumentActivityEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ProcumentActivityEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ProcumentActivityEditLogRepository
     */
    protected $procumentActivityEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->procumentActivityEditLogRepo = \App::make(ProcumentActivityEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_procument_activity_edit_log()
    {
        $procumentActivityEditLog = factory(ProcumentActivityEditLog::class)->make()->toArray();

        $createdProcumentActivityEditLog = $this->procumentActivityEditLogRepo->create($procumentActivityEditLog);

        $createdProcumentActivityEditLog = $createdProcumentActivityEditLog->toArray();
        $this->assertArrayHasKey('id', $createdProcumentActivityEditLog);
        $this->assertNotNull($createdProcumentActivityEditLog['id'], 'Created ProcumentActivityEditLog must have id specified');
        $this->assertNotNull(ProcumentActivityEditLog::find($createdProcumentActivityEditLog['id']), 'ProcumentActivityEditLog with given id must be in DB');
        $this->assertModelData($procumentActivityEditLog, $createdProcumentActivityEditLog);
    }

    /**
     * @test read
     */
    public function test_read_procument_activity_edit_log()
    {
        $procumentActivityEditLog = factory(ProcumentActivityEditLog::class)->create();

        $dbProcumentActivityEditLog = $this->procumentActivityEditLogRepo->find($procumentActivityEditLog->id);

        $dbProcumentActivityEditLog = $dbProcumentActivityEditLog->toArray();
        $this->assertModelData($procumentActivityEditLog->toArray(), $dbProcumentActivityEditLog);
    }

    /**
     * @test update
     */
    public function test_update_procument_activity_edit_log()
    {
        $procumentActivityEditLog = factory(ProcumentActivityEditLog::class)->create();
        $fakeProcumentActivityEditLog = factory(ProcumentActivityEditLog::class)->make()->toArray();

        $updatedProcumentActivityEditLog = $this->procumentActivityEditLogRepo->update($fakeProcumentActivityEditLog, $procumentActivityEditLog->id);

        $this->assertModelData($fakeProcumentActivityEditLog, $updatedProcumentActivityEditLog->toArray());
        $dbProcumentActivityEditLog = $this->procumentActivityEditLogRepo->find($procumentActivityEditLog->id);
        $this->assertModelData($fakeProcumentActivityEditLog, $dbProcumentActivityEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_procument_activity_edit_log()
    {
        $procumentActivityEditLog = factory(ProcumentActivityEditLog::class)->create();

        $resp = $this->procumentActivityEditLogRepo->delete($procumentActivityEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(ProcumentActivityEditLog::find($procumentActivityEditLog->id), 'ProcumentActivityEditLog should not exist in DB');
    }
}
