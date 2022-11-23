<?php namespace Tests\Repositories;

use App\Models\CashFlowReportDetail;
use App\Repositories\CashFlowReportDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CashFlowReportDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CashFlowReportDetailRepository
     */
    protected $cashFlowReportDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->cashFlowReportDetailRepo = \App::make(CashFlowReportDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_cash_flow_report_detail()
    {
        $cashFlowReportDetail = factory(CashFlowReportDetail::class)->make()->toArray();

        $createdCashFlowReportDetail = $this->cashFlowReportDetailRepo->create($cashFlowReportDetail);

        $createdCashFlowReportDetail = $createdCashFlowReportDetail->toArray();
        $this->assertArrayHasKey('id', $createdCashFlowReportDetail);
        $this->assertNotNull($createdCashFlowReportDetail['id'], 'Created CashFlowReportDetail must have id specified');
        $this->assertNotNull(CashFlowReportDetail::find($createdCashFlowReportDetail['id']), 'CashFlowReportDetail with given id must be in DB');
        $this->assertModelData($cashFlowReportDetail, $createdCashFlowReportDetail);
    }

    /**
     * @test read
     */
    public function test_read_cash_flow_report_detail()
    {
        $cashFlowReportDetail = factory(CashFlowReportDetail::class)->create();

        $dbCashFlowReportDetail = $this->cashFlowReportDetailRepo->find($cashFlowReportDetail->id);

        $dbCashFlowReportDetail = $dbCashFlowReportDetail->toArray();
        $this->assertModelData($cashFlowReportDetail->toArray(), $dbCashFlowReportDetail);
    }

    /**
     * @test update
     */
    public function test_update_cash_flow_report_detail()
    {
        $cashFlowReportDetail = factory(CashFlowReportDetail::class)->create();
        $fakeCashFlowReportDetail = factory(CashFlowReportDetail::class)->make()->toArray();

        $updatedCashFlowReportDetail = $this->cashFlowReportDetailRepo->update($fakeCashFlowReportDetail, $cashFlowReportDetail->id);

        $this->assertModelData($fakeCashFlowReportDetail, $updatedCashFlowReportDetail->toArray());
        $dbCashFlowReportDetail = $this->cashFlowReportDetailRepo->find($cashFlowReportDetail->id);
        $this->assertModelData($fakeCashFlowReportDetail, $dbCashFlowReportDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_cash_flow_report_detail()
    {
        $cashFlowReportDetail = factory(CashFlowReportDetail::class)->create();

        $resp = $this->cashFlowReportDetailRepo->delete($cashFlowReportDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(CashFlowReportDetail::find($cashFlowReportDetail->id), 'CashFlowReportDetail should not exist in DB');
    }
}
