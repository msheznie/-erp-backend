<?php namespace Tests\Repositories;

use App\Models\CashFlowTemplate;
use App\Repositories\CashFlowTemplateRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CashFlowTemplateRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CashFlowTemplateRepository
     */
    protected $cashFlowTemplateRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->cashFlowTemplateRepo = \App::make(CashFlowTemplateRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_cash_flow_template()
    {
        $cashFlowTemplate = factory(CashFlowTemplate::class)->make()->toArray();

        $createdCashFlowTemplate = $this->cashFlowTemplateRepo->create($cashFlowTemplate);

        $createdCashFlowTemplate = $createdCashFlowTemplate->toArray();
        $this->assertArrayHasKey('id', $createdCashFlowTemplate);
        $this->assertNotNull($createdCashFlowTemplate['id'], 'Created CashFlowTemplate must have id specified');
        $this->assertNotNull(CashFlowTemplate::find($createdCashFlowTemplate['id']), 'CashFlowTemplate with given id must be in DB');
        $this->assertModelData($cashFlowTemplate, $createdCashFlowTemplate);
    }

    /**
     * @test read
     */
    public function test_read_cash_flow_template()
    {
        $cashFlowTemplate = factory(CashFlowTemplate::class)->create();

        $dbCashFlowTemplate = $this->cashFlowTemplateRepo->find($cashFlowTemplate->id);

        $dbCashFlowTemplate = $dbCashFlowTemplate->toArray();
        $this->assertModelData($cashFlowTemplate->toArray(), $dbCashFlowTemplate);
    }

    /**
     * @test update
     */
    public function test_update_cash_flow_template()
    {
        $cashFlowTemplate = factory(CashFlowTemplate::class)->create();
        $fakeCashFlowTemplate = factory(CashFlowTemplate::class)->make()->toArray();

        $updatedCashFlowTemplate = $this->cashFlowTemplateRepo->update($fakeCashFlowTemplate, $cashFlowTemplate->id);

        $this->assertModelData($fakeCashFlowTemplate, $updatedCashFlowTemplate->toArray());
        $dbCashFlowTemplate = $this->cashFlowTemplateRepo->find($cashFlowTemplate->id);
        $this->assertModelData($fakeCashFlowTemplate, $dbCashFlowTemplate->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_cash_flow_template()
    {
        $cashFlowTemplate = factory(CashFlowTemplate::class)->create();

        $resp = $this->cashFlowTemplateRepo->delete($cashFlowTemplate->id);

        $this->assertTrue($resp);
        $this->assertNull(CashFlowTemplate::find($cashFlowTemplate->id), 'CashFlowTemplate should not exist in DB');
    }
}
