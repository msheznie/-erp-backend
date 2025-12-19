<?php namespace Tests\Repositories;

use App\Models\BudgetControlLink;
use App\Repositories\BudgetControlLinkRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BudgetControlLinkRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetControlLinkRepository
     */
    protected $budgetControlLinkRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->budgetControlLinkRepo = \App::make(BudgetControlLinkRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_budget_control_link()
    {
        $budgetControlLink = factory(BudgetControlLink::class)->make()->toArray();

        $createdBudgetControlLink = $this->budgetControlLinkRepo->create($budgetControlLink);

        $createdBudgetControlLink = $createdBudgetControlLink->toArray();
        $this->assertArrayHasKey('id', $createdBudgetControlLink);
        $this->assertNotNull($createdBudgetControlLink['id'], 'Created BudgetControlLink must have id specified');
        $this->assertNotNull(BudgetControlLink::find($createdBudgetControlLink['id']), 'BudgetControlLink with given id must be in DB');
        $this->assertModelData($budgetControlLink, $createdBudgetControlLink);
    }

    /**
     * @test read
     */
    public function test_read_budget_control_link()
    {
        $budgetControlLink = factory(BudgetControlLink::class)->create();

        $dbBudgetControlLink = $this->budgetControlLinkRepo->find($budgetControlLink->id);

        $dbBudgetControlLink = $dbBudgetControlLink->toArray();
        $this->assertModelData($budgetControlLink->toArray(), $dbBudgetControlLink);
    }

    /**
     * @test update
     */
    public function test_update_budget_control_link()
    {
        $budgetControlLink = factory(BudgetControlLink::class)->create();
        $fakeBudgetControlLink = factory(BudgetControlLink::class)->make()->toArray();

        $updatedBudgetControlLink = $this->budgetControlLinkRepo->update($fakeBudgetControlLink, $budgetControlLink->id);

        $this->assertModelData($fakeBudgetControlLink, $updatedBudgetControlLink->toArray());
        $dbBudgetControlLink = $this->budgetControlLinkRepo->find($budgetControlLink->id);
        $this->assertModelData($fakeBudgetControlLink, $dbBudgetControlLink->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_budget_control_link()
    {
        $budgetControlLink = factory(BudgetControlLink::class)->create();

        $resp = $this->budgetControlLinkRepo->delete($budgetControlLink->id);

        $this->assertTrue($resp);
        $this->assertNull(BudgetControlLink::find($budgetControlLink->id), 'BudgetControlLink should not exist in DB');
    }
}
