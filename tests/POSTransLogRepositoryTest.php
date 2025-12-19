<?php namespace Tests\Repositories;

use App\Models\POSTransLog;
use App\Repositories\POSTransLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSTransLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSTransLogRepository
     */
    protected $pOSTransLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSTransLogRepo = \App::make(POSTransLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_trans_log()
    {
        $pOSTransLog = factory(POSTransLog::class)->make()->toArray();

        $createdPOSTransLog = $this->pOSTransLogRepo->create($pOSTransLog);

        $createdPOSTransLog = $createdPOSTransLog->toArray();
        $this->assertArrayHasKey('id', $createdPOSTransLog);
        $this->assertNotNull($createdPOSTransLog['id'], 'Created POSTransLog must have id specified');
        $this->assertNotNull(POSTransLog::find($createdPOSTransLog['id']), 'POSTransLog with given id must be in DB');
        $this->assertModelData($pOSTransLog, $createdPOSTransLog);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_trans_log()
    {
        $pOSTransLog = factory(POSTransLog::class)->create();

        $dbPOSTransLog = $this->pOSTransLogRepo->find($pOSTransLog->id);

        $dbPOSTransLog = $dbPOSTransLog->toArray();
        $this->assertModelData($pOSTransLog->toArray(), $dbPOSTransLog);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_trans_log()
    {
        $pOSTransLog = factory(POSTransLog::class)->create();
        $fakePOSTransLog = factory(POSTransLog::class)->make()->toArray();

        $updatedPOSTransLog = $this->pOSTransLogRepo->update($fakePOSTransLog, $pOSTransLog->id);

        $this->assertModelData($fakePOSTransLog, $updatedPOSTransLog->toArray());
        $dbPOSTransLog = $this->pOSTransLogRepo->find($pOSTransLog->id);
        $this->assertModelData($fakePOSTransLog, $dbPOSTransLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_trans_log()
    {
        $pOSTransLog = factory(POSTransLog::class)->create();

        $resp = $this->pOSTransLogRepo->delete($pOSTransLog->id);

        $this->assertTrue($resp);
        $this->assertNull(POSTransLog::find($pOSTransLog->id), 'POSTransLog should not exist in DB');
    }
}
