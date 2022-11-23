<?php namespace Tests\Repositories;

use App\Models\CashFlowTemplateDetail;
use App\Repositories\CashFlowTemplateDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CashFlowTemplateDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CashFlowTemplateDetailRepository
     */
    protected $cashFlowTemplateDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->cashFlowTemplateDetailRepo = \App::make(CashFlowTemplateDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_cash_flow_template_detail()
    {
        $cashFlowTemplateDetail = factory(CashFlowTemplateDetail::class)->make()->toArray();

        $createdCashFlowTemplateDetail = $this->cashFlowTemplateDetailRepo->create($cashFlowTemplateDetail);

        $createdCashFlowTemplateDetail = $createdCashFlowTemplateDetail->toArray();
        $this->assertArrayHasKey('id', $createdCashFlowTemplateDetail);
        $this->assertNotNull($createdCashFlowTemplateDetail['id'], 'Created CashFlowTemplateDetail must have id specified');
        $this->assertNotNull(CashFlowTemplateDetail::find($createdCashFlowTemplateDetail['id']), 'CashFlowTemplateDetail with given id must be in DB');
        $this->assertModelData($cashFlowTemplateDetail, $createdCashFlowTemplateDetail);
    }

    /**
     * @test read
     */
    public function test_read_cash_flow_template_detail()
    {
        $cashFlowTemplateDetail = factory(CashFlowTemplateDetail::class)->create();

        $dbCashFlowTemplateDetail = $this->cashFlowTemplateDetailRepo->find($cashFlowTemplateDetail->id);

        $dbCashFlowTemplateDetail = $dbCashFlowTemplateDetail->toArray();
        $this->assertModelData($cashFlowTemplateDetail->toArray(), $dbCashFlowTemplateDetail);
    }

    /**
     * @test update
     */
    public function test_update_cash_flow_template_detail()
    {
        $cashFlowTemplateDetail = factory(CashFlowTemplateDetail::class)->create();
        $fakeCashFlowTemplateDetail = factory(CashFlowTemplateDetail::class)->make()->toArray();

        $updatedCashFlowTemplateDetail = $this->cashFlowTemplateDetailRepo->update($fakeCashFlowTemplateDetail, $cashFlowTemplateDetail->id);

        $this->assertModelData($fakeCashFlowTemplateDetail, $updatedCashFlowTemplateDetail->toArray());
        $dbCashFlowTemplateDetail = $this->cashFlowTemplateDetailRepo->find($cashFlowTemplateDetail->id);
        $this->assertModelData($fakeCashFlowTemplateDetail, $dbCashFlowTemplateDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_cash_flow_template_detail()
    {
        $cashFlowTemplateDetail = factory(CashFlowTemplateDetail::class)->create();

        $resp = $this->cashFlowTemplateDetailRepo->delete($cashFlowTemplateDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(CashFlowTemplateDetail::find($cashFlowTemplateDetail->id), 'CashFlowTemplateDetail should not exist in DB');
    }
}
