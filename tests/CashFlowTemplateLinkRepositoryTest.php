<?php namespace Tests\Repositories;

use App\Models\CashFlowTemplateLink;
use App\Repositories\CashFlowTemplateLinkRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CashFlowTemplateLinkRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CashFlowTemplateLinkRepository
     */
    protected $cashFlowTemplateLinkRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->cashFlowTemplateLinkRepo = \App::make(CashFlowTemplateLinkRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_cash_flow_template_link()
    {
        $cashFlowTemplateLink = factory(CashFlowTemplateLink::class)->make()->toArray();

        $createdCashFlowTemplateLink = $this->cashFlowTemplateLinkRepo->create($cashFlowTemplateLink);

        $createdCashFlowTemplateLink = $createdCashFlowTemplateLink->toArray();
        $this->assertArrayHasKey('id', $createdCashFlowTemplateLink);
        $this->assertNotNull($createdCashFlowTemplateLink['id'], 'Created CashFlowTemplateLink must have id specified');
        $this->assertNotNull(CashFlowTemplateLink::find($createdCashFlowTemplateLink['id']), 'CashFlowTemplateLink with given id must be in DB');
        $this->assertModelData($cashFlowTemplateLink, $createdCashFlowTemplateLink);
    }

    /**
     * @test read
     */
    public function test_read_cash_flow_template_link()
    {
        $cashFlowTemplateLink = factory(CashFlowTemplateLink::class)->create();

        $dbCashFlowTemplateLink = $this->cashFlowTemplateLinkRepo->find($cashFlowTemplateLink->id);

        $dbCashFlowTemplateLink = $dbCashFlowTemplateLink->toArray();
        $this->assertModelData($cashFlowTemplateLink->toArray(), $dbCashFlowTemplateLink);
    }

    /**
     * @test update
     */
    public function test_update_cash_flow_template_link()
    {
        $cashFlowTemplateLink = factory(CashFlowTemplateLink::class)->create();
        $fakeCashFlowTemplateLink = factory(CashFlowTemplateLink::class)->make()->toArray();

        $updatedCashFlowTemplateLink = $this->cashFlowTemplateLinkRepo->update($fakeCashFlowTemplateLink, $cashFlowTemplateLink->id);

        $this->assertModelData($fakeCashFlowTemplateLink, $updatedCashFlowTemplateLink->toArray());
        $dbCashFlowTemplateLink = $this->cashFlowTemplateLinkRepo->find($cashFlowTemplateLink->id);
        $this->assertModelData($fakeCashFlowTemplateLink, $dbCashFlowTemplateLink->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_cash_flow_template_link()
    {
        $cashFlowTemplateLink = factory(CashFlowTemplateLink::class)->create();

        $resp = $this->cashFlowTemplateLinkRepo->delete($cashFlowTemplateLink->id);

        $this->assertTrue($resp);
        $this->assertNull(CashFlowTemplateLink::find($cashFlowTemplateLink->id), 'CashFlowTemplateLink should not exist in DB');
    }
}
