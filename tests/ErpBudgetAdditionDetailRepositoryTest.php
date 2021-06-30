<?php namespace Tests\Repositories;

use App\Models\ErpBudgetAdditionDetail;
use App\Repositories\ErpBudgetAdditionDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ErpBudgetAdditionDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ErpBudgetAdditionDetailRepository
     */
    protected $erpBudgetAdditionDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->erpBudgetAdditionDetailRepo = \App::make(ErpBudgetAdditionDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_erp_budget_addition_detail()
    {
        $erpBudgetAdditionDetail = factory(ErpBudgetAdditionDetail::class)->make()->toArray();

        $createdErpBudgetAdditionDetail = $this->erpBudgetAdditionDetailRepo->create($erpBudgetAdditionDetail);

        $createdErpBudgetAdditionDetail = $createdErpBudgetAdditionDetail->toArray();
        $this->assertArrayHasKey('id', $createdErpBudgetAdditionDetail);
        $this->assertNotNull($createdErpBudgetAdditionDetail['id'], 'Created ErpBudgetAdditionDetail must have id specified');
        $this->assertNotNull(ErpBudgetAdditionDetail::find($createdErpBudgetAdditionDetail['id']), 'ErpBudgetAdditionDetail with given id must be in DB');
        $this->assertModelData($erpBudgetAdditionDetail, $createdErpBudgetAdditionDetail);
    }

    /**
     * @test read
     */
    public function test_read_erp_budget_addition_detail()
    {
        $erpBudgetAdditionDetail = factory(ErpBudgetAdditionDetail::class)->create();

        $dbErpBudgetAdditionDetail = $this->erpBudgetAdditionDetailRepo->find($erpBudgetAdditionDetail->id);

        $dbErpBudgetAdditionDetail = $dbErpBudgetAdditionDetail->toArray();
        $this->assertModelData($erpBudgetAdditionDetail->toArray(), $dbErpBudgetAdditionDetail);
    }

    /**
     * @test update
     */
    public function test_update_erp_budget_addition_detail()
    {
        $erpBudgetAdditionDetail = factory(ErpBudgetAdditionDetail::class)->create();
        $fakeErpBudgetAdditionDetail = factory(ErpBudgetAdditionDetail::class)->make()->toArray();

        $updatedErpBudgetAdditionDetail = $this->erpBudgetAdditionDetailRepo->update($fakeErpBudgetAdditionDetail, $erpBudgetAdditionDetail->id);

        $this->assertModelData($fakeErpBudgetAdditionDetail, $updatedErpBudgetAdditionDetail->toArray());
        $dbErpBudgetAdditionDetail = $this->erpBudgetAdditionDetailRepo->find($erpBudgetAdditionDetail->id);
        $this->assertModelData($fakeErpBudgetAdditionDetail, $dbErpBudgetAdditionDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_erp_budget_addition_detail()
    {
        $erpBudgetAdditionDetail = factory(ErpBudgetAdditionDetail::class)->create();

        $resp = $this->erpBudgetAdditionDetailRepo->delete($erpBudgetAdditionDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(ErpBudgetAdditionDetail::find($erpBudgetAdditionDetail->id), 'ErpBudgetAdditionDetail should not exist in DB');
    }
}
