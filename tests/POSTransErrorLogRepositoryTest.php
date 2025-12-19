<?php namespace Tests\Repositories;

use App\Models\POSTransErrorLog;
use App\Repositories\POSTransErrorLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSTransErrorLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSTransErrorLogRepository
     */
    protected $pOSTransErrorLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSTransErrorLogRepo = \App::make(POSTransErrorLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_trans_error_log()
    {
        $pOSTransErrorLog = factory(POSTransErrorLog::class)->make()->toArray();

        $createdPOSTransErrorLog = $this->pOSTransErrorLogRepo->create($pOSTransErrorLog);

        $createdPOSTransErrorLog = $createdPOSTransErrorLog->toArray();
        $this->assertArrayHasKey('id', $createdPOSTransErrorLog);
        $this->assertNotNull($createdPOSTransErrorLog['id'], 'Created POSTransErrorLog must have id specified');
        $this->assertNotNull(POSTransErrorLog::find($createdPOSTransErrorLog['id']), 'POSTransErrorLog with given id must be in DB');
        $this->assertModelData($pOSTransErrorLog, $createdPOSTransErrorLog);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_trans_error_log()
    {
        $pOSTransErrorLog = factory(POSTransErrorLog::class)->create();

        $dbPOSTransErrorLog = $this->pOSTransErrorLogRepo->find($pOSTransErrorLog->id);

        $dbPOSTransErrorLog = $dbPOSTransErrorLog->toArray();
        $this->assertModelData($pOSTransErrorLog->toArray(), $dbPOSTransErrorLog);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_trans_error_log()
    {
        $pOSTransErrorLog = factory(POSTransErrorLog::class)->create();
        $fakePOSTransErrorLog = factory(POSTransErrorLog::class)->make()->toArray();

        $updatedPOSTransErrorLog = $this->pOSTransErrorLogRepo->update($fakePOSTransErrorLog, $pOSTransErrorLog->id);

        $this->assertModelData($fakePOSTransErrorLog, $updatedPOSTransErrorLog->toArray());
        $dbPOSTransErrorLog = $this->pOSTransErrorLogRepo->find($pOSTransErrorLog->id);
        $this->assertModelData($fakePOSTransErrorLog, $dbPOSTransErrorLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_trans_error_log()
    {
        $pOSTransErrorLog = factory(POSTransErrorLog::class)->create();

        $resp = $this->pOSTransErrorLogRepo->delete($pOSTransErrorLog->id);

        $this->assertTrue($resp);
        $this->assertNull(POSTransErrorLog::find($pOSTransErrorLog->id), 'POSTransErrorLog should not exist in DB');
    }
}
