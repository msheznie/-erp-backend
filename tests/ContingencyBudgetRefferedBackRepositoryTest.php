<?php namespace Tests\Repositories;

use App\Models\ContingencyBudgetRefferedBack;
use App\Repositories\ContingencyBudgetRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ContingencyBudgetRefferedBackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ContingencyBudgetRefferedBackRepository
     */
    protected $contingencyBudgetRefferedBackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->contingencyBudgetRefferedBackRepo = \App::make(ContingencyBudgetRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_contingency_budget_reffered_back()
    {
        $contingencyBudgetRefferedBack = factory(ContingencyBudgetRefferedBack::class)->make()->toArray();

        $createdContingencyBudgetRefferedBack = $this->contingencyBudgetRefferedBackRepo->create($contingencyBudgetRefferedBack);

        $createdContingencyBudgetRefferedBack = $createdContingencyBudgetRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdContingencyBudgetRefferedBack);
        $this->assertNotNull($createdContingencyBudgetRefferedBack['id'], 'Created ContingencyBudgetRefferedBack must have id specified');
        $this->assertNotNull(ContingencyBudgetRefferedBack::find($createdContingencyBudgetRefferedBack['id']), 'ContingencyBudgetRefferedBack with given id must be in DB');
        $this->assertModelData($contingencyBudgetRefferedBack, $createdContingencyBudgetRefferedBack);
    }

    /**
     * @test read
     */
    public function test_read_contingency_budget_reffered_back()
    {
        $contingencyBudgetRefferedBack = factory(ContingencyBudgetRefferedBack::class)->create();

        $dbContingencyBudgetRefferedBack = $this->contingencyBudgetRefferedBackRepo->find($contingencyBudgetRefferedBack->id);

        $dbContingencyBudgetRefferedBack = $dbContingencyBudgetRefferedBack->toArray();
        $this->assertModelData($contingencyBudgetRefferedBack->toArray(), $dbContingencyBudgetRefferedBack);
    }

    /**
     * @test update
     */
    public function test_update_contingency_budget_reffered_back()
    {
        $contingencyBudgetRefferedBack = factory(ContingencyBudgetRefferedBack::class)->create();
        $fakeContingencyBudgetRefferedBack = factory(ContingencyBudgetRefferedBack::class)->make()->toArray();

        $updatedContingencyBudgetRefferedBack = $this->contingencyBudgetRefferedBackRepo->update($fakeContingencyBudgetRefferedBack, $contingencyBudgetRefferedBack->id);

        $this->assertModelData($fakeContingencyBudgetRefferedBack, $updatedContingencyBudgetRefferedBack->toArray());
        $dbContingencyBudgetRefferedBack = $this->contingencyBudgetRefferedBackRepo->find($contingencyBudgetRefferedBack->id);
        $this->assertModelData($fakeContingencyBudgetRefferedBack, $dbContingencyBudgetRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_contingency_budget_reffered_back()
    {
        $contingencyBudgetRefferedBack = factory(ContingencyBudgetRefferedBack::class)->create();

        $resp = $this->contingencyBudgetRefferedBackRepo->delete($contingencyBudgetRefferedBack->id);

        $this->assertTrue($resp);
        $this->assertNull(ContingencyBudgetRefferedBack::find($contingencyBudgetRefferedBack->id), 'ContingencyBudgetRefferedBack should not exist in DB');
    }
}
