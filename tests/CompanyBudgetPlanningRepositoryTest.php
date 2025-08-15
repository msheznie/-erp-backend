<?php namespace Tests\Repositories;

use App\Models\CompanyBudgetPlanning;
use App\Repositories\CompanyBudgetPlanningRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CompanyBudgetPlanningRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CompanyBudgetPlanningRepository
     */
    protected $companyBudgetPlanningRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->companyBudgetPlanningRepo = \App::make(CompanyBudgetPlanningRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_company_budget_planning()
    {
        $companyBudgetPlanning = factory(CompanyBudgetPlanning::class)->make()->toArray();

        $createdCompanyBudgetPlanning = $this->companyBudgetPlanningRepo->create($companyBudgetPlanning);

        $createdCompanyBudgetPlanning = $createdCompanyBudgetPlanning->toArray();
        $this->assertArrayHasKey('id', $createdCompanyBudgetPlanning);
        $this->assertNotNull($createdCompanyBudgetPlanning['id'], 'Created CompanyBudgetPlanning must have id specified');
        $this->assertNotNull(CompanyBudgetPlanning::find($createdCompanyBudgetPlanning['id']), 'CompanyBudgetPlanning with given id must be in DB');
        $this->assertModelData($companyBudgetPlanning, $createdCompanyBudgetPlanning);
    }

    /**
     * @test read
     */
    public function test_read_company_budget_planning()
    {
        $companyBudgetPlanning = factory(CompanyBudgetPlanning::class)->create();

        $dbCompanyBudgetPlanning = $this->companyBudgetPlanningRepo->find($companyBudgetPlanning->id);

        $dbCompanyBudgetPlanning = $dbCompanyBudgetPlanning->toArray();
        $this->assertModelData($companyBudgetPlanning->toArray(), $dbCompanyBudgetPlanning);
    }

    /**
     * @test update
     */
    public function test_update_company_budget_planning()
    {
        $companyBudgetPlanning = factory(CompanyBudgetPlanning::class)->create();
        $fakeCompanyBudgetPlanning = factory(CompanyBudgetPlanning::class)->make()->toArray();

        $updatedCompanyBudgetPlanning = $this->companyBudgetPlanningRepo->update($fakeCompanyBudgetPlanning, $companyBudgetPlanning->id);

        $this->assertModelData($fakeCompanyBudgetPlanning, $updatedCompanyBudgetPlanning->toArray());
        $dbCompanyBudgetPlanning = $this->companyBudgetPlanningRepo->find($companyBudgetPlanning->id);
        $this->assertModelData($fakeCompanyBudgetPlanning, $dbCompanyBudgetPlanning->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_company_budget_planning()
    {
        $companyBudgetPlanning = factory(CompanyBudgetPlanning::class)->create();

        $resp = $this->companyBudgetPlanningRepo->delete($companyBudgetPlanning->id);

        $this->assertTrue($resp);
        $this->assertNull(CompanyBudgetPlanning::find($companyBudgetPlanning->id), 'CompanyBudgetPlanning should not exist in DB');
    }
}
