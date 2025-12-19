<?php namespace Tests\Repositories;

use App\Models\ErpBudgetAddition;
use App\Repositories\ErpBudgetAdditionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ErpBudgetAdditionRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ErpBudgetAdditionRepository
     */
    protected $erpBudgetAdditionRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->erpBudgetAdditionRepo = \App::make(ErpBudgetAdditionRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_erp_budget_addition()
    {
        $erpBudgetAddition = factory(ErpBudgetAddition::class)->make()->toArray();

        $createdErpBudgetAddition = $this->erpBudgetAdditionRepo->create($erpBudgetAddition);

        $createdErpBudgetAddition = $createdErpBudgetAddition->toArray();
        $this->assertArrayHasKey('id', $createdErpBudgetAddition);
        $this->assertNotNull($createdErpBudgetAddition['id'], 'Created ErpBudgetAddition must have id specified');
        $this->assertNotNull(ErpBudgetAddition::find($createdErpBudgetAddition['id']), 'ErpBudgetAddition with given id must be in DB');
        $this->assertModelData($erpBudgetAddition, $createdErpBudgetAddition);
    }

    /**
     * @test read
     */
    public function test_read_erp_budget_addition()
    {
        $erpBudgetAddition = factory(ErpBudgetAddition::class)->create();

        $dbErpBudgetAddition = $this->erpBudgetAdditionRepo->find($erpBudgetAddition->id);

        $dbErpBudgetAddition = $dbErpBudgetAddition->toArray();
        $this->assertModelData($erpBudgetAddition->toArray(), $dbErpBudgetAddition);
    }

    /**
     * @test update
     */
    public function test_update_erp_budget_addition()
    {
        $erpBudgetAddition = factory(ErpBudgetAddition::class)->create();
        $fakeErpBudgetAddition = factory(ErpBudgetAddition::class)->make()->toArray();

        $updatedErpBudgetAddition = $this->erpBudgetAdditionRepo->update($fakeErpBudgetAddition, $erpBudgetAddition->id);

        $this->assertModelData($fakeErpBudgetAddition, $updatedErpBudgetAddition->toArray());
        $dbErpBudgetAddition = $this->erpBudgetAdditionRepo->find($erpBudgetAddition->id);
        $this->assertModelData($fakeErpBudgetAddition, $dbErpBudgetAddition->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_erp_budget_addition()
    {
        $erpBudgetAddition = factory(ErpBudgetAddition::class)->create();

        $resp = $this->erpBudgetAdditionRepo->delete($erpBudgetAddition->id);

        $this->assertTrue($resp);
        $this->assertNull(ErpBudgetAddition::find($erpBudgetAddition->id), 'ErpBudgetAddition should not exist in DB');
    }
}
