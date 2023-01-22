<?php namespace Tests\Repositories;

use App\Models\PdcLogPrintedHistory;
use App\Repositories\PdcLogPrintedHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PdcLogPrintedHistoryRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PdcLogPrintedHistoryRepository
     */
    protected $pdcLogPrintedHistoryRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pdcLogPrintedHistoryRepo = \App::make(PdcLogPrintedHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_pdc_log_printed_history()
    {
        $pdcLogPrintedHistory = factory(PdcLogPrintedHistory::class)->make()->toArray();

        $createdPdcLogPrintedHistory = $this->pdcLogPrintedHistoryRepo->create($pdcLogPrintedHistory);

        $createdPdcLogPrintedHistory = $createdPdcLogPrintedHistory->toArray();
        $this->assertArrayHasKey('id', $createdPdcLogPrintedHistory);
        $this->assertNotNull($createdPdcLogPrintedHistory['id'], 'Created PdcLogPrintedHistory must have id specified');
        $this->assertNotNull(PdcLogPrintedHistory::find($createdPdcLogPrintedHistory['id']), 'PdcLogPrintedHistory with given id must be in DB');
        $this->assertModelData($pdcLogPrintedHistory, $createdPdcLogPrintedHistory);
    }

    /**
     * @test read
     */
    public function test_read_pdc_log_printed_history()
    {
        $pdcLogPrintedHistory = factory(PdcLogPrintedHistory::class)->create();

        $dbPdcLogPrintedHistory = $this->pdcLogPrintedHistoryRepo->find($pdcLogPrintedHistory->id);

        $dbPdcLogPrintedHistory = $dbPdcLogPrintedHistory->toArray();
        $this->assertModelData($pdcLogPrintedHistory->toArray(), $dbPdcLogPrintedHistory);
    }

    /**
     * @test update
     */
    public function test_update_pdc_log_printed_history()
    {
        $pdcLogPrintedHistory = factory(PdcLogPrintedHistory::class)->create();
        $fakePdcLogPrintedHistory = factory(PdcLogPrintedHistory::class)->make()->toArray();

        $updatedPdcLogPrintedHistory = $this->pdcLogPrintedHistoryRepo->update($fakePdcLogPrintedHistory, $pdcLogPrintedHistory->id);

        $this->assertModelData($fakePdcLogPrintedHistory, $updatedPdcLogPrintedHistory->toArray());
        $dbPdcLogPrintedHistory = $this->pdcLogPrintedHistoryRepo->find($pdcLogPrintedHistory->id);
        $this->assertModelData($fakePdcLogPrintedHistory, $dbPdcLogPrintedHistory->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_pdc_log_printed_history()
    {
        $pdcLogPrintedHistory = factory(PdcLogPrintedHistory::class)->create();

        $resp = $this->pdcLogPrintedHistoryRepo->delete($pdcLogPrintedHistory->id);

        $this->assertTrue($resp);
        $this->assertNull(PdcLogPrintedHistory::find($pdcLogPrintedHistory->id), 'PdcLogPrintedHistory should not exist in DB');
    }
}
