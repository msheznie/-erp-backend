<?php namespace Tests\Repositories;

use App\Models\CashFlowReport;
use App\Repositories\CashFlowReportRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CashFlowReportRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CashFlowReportRepository
     */
    protected $cashFlowReportRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->cashFlowReportRepo = \App::make(CashFlowReportRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_cash_flow_report()
    {
        $cashFlowReport = factory(CashFlowReport::class)->make()->toArray();

        $createdCashFlowReport = $this->cashFlowReportRepo->create($cashFlowReport);

        $createdCashFlowReport = $createdCashFlowReport->toArray();
        $this->assertArrayHasKey('id', $createdCashFlowReport);
        $this->assertNotNull($createdCashFlowReport['id'], 'Created CashFlowReport must have id specified');
        $this->assertNotNull(CashFlowReport::find($createdCashFlowReport['id']), 'CashFlowReport with given id must be in DB');
        $this->assertModelData($cashFlowReport, $createdCashFlowReport);
    }

    /**
     * @test read
     */
    public function test_read_cash_flow_report()
    {
        $cashFlowReport = factory(CashFlowReport::class)->create();

        $dbCashFlowReport = $this->cashFlowReportRepo->find($cashFlowReport->id);

        $dbCashFlowReport = $dbCashFlowReport->toArray();
        $this->assertModelData($cashFlowReport->toArray(), $dbCashFlowReport);
    }

    /**
     * @test update
     */
    public function test_update_cash_flow_report()
    {
        $cashFlowReport = factory(CashFlowReport::class)->create();
        $fakeCashFlowReport = factory(CashFlowReport::class)->make()->toArray();

        $updatedCashFlowReport = $this->cashFlowReportRepo->update($fakeCashFlowReport, $cashFlowReport->id);

        $this->assertModelData($fakeCashFlowReport, $updatedCashFlowReport->toArray());
        $dbCashFlowReport = $this->cashFlowReportRepo->find($cashFlowReport->id);
        $this->assertModelData($fakeCashFlowReport, $dbCashFlowReport->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_cash_flow_report()
    {
        $cashFlowReport = factory(CashFlowReport::class)->create();

        $resp = $this->cashFlowReportRepo->delete($cashFlowReport->id);

        $this->assertTrue($resp);
        $this->assertNull(CashFlowReport::find($cashFlowReport->id), 'CashFlowReport should not exist in DB');
    }
}
