<?php namespace Tests\Repositories;

use App\Models\PdcLog;
use App\Repositories\PdcLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PdcLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PdcLogRepository
     */
    protected $pdcLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pdcLogRepo = \App::make(PdcLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_pdc_log()
    {
        $pdcLog = factory(PdcLog::class)->make()->toArray();

        $createdPdcLog = $this->pdcLogRepo->create($pdcLog);

        $createdPdcLog = $createdPdcLog->toArray();
        $this->assertArrayHasKey('id', $createdPdcLog);
        $this->assertNotNull($createdPdcLog['id'], 'Created PdcLog must have id specified');
        $this->assertNotNull(PdcLog::find($createdPdcLog['id']), 'PdcLog with given id must be in DB');
        $this->assertModelData($pdcLog, $createdPdcLog);
    }

    /**
     * @test read
     */
    public function test_read_pdc_log()
    {
        $pdcLog = factory(PdcLog::class)->create();

        $dbPdcLog = $this->pdcLogRepo->find($pdcLog->id);

        $dbPdcLog = $dbPdcLog->toArray();
        $this->assertModelData($pdcLog->toArray(), $dbPdcLog);
    }

    /**
     * @test update
     */
    public function test_update_pdc_log()
    {
        $pdcLog = factory(PdcLog::class)->create();
        $fakePdcLog = factory(PdcLog::class)->make()->toArray();

        $updatedPdcLog = $this->pdcLogRepo->update($fakePdcLog, $pdcLog->id);

        $this->assertModelData($fakePdcLog, $updatedPdcLog->toArray());
        $dbPdcLog = $this->pdcLogRepo->find($pdcLog->id);
        $this->assertModelData($fakePdcLog, $dbPdcLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_pdc_log()
    {
        $pdcLog = factory(PdcLog::class)->create();

        $resp = $this->pdcLogRepo->delete($pdcLog->id);

        $this->assertTrue($resp);
        $this->assertNull(PdcLog::find($pdcLog->id), 'PdcLog should not exist in DB');
    }
}
