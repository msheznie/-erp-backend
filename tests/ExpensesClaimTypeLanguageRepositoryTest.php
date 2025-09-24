<?php namespace Tests\Repositories;

use App\Models\ExpensesClaimTypeLanguage;
use App\Repositories\ExpensesClaimTypeLanguageRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ExpensesClaimTypeLanguageRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ExpensesClaimTypeLanguageRepository
     */
    protected $expensesClaimTypeLanguageRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->expensesClaimTypeLanguageRepo = \App::make(ExpensesClaimTypeLanguageRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_expenses_claim_type_language()
    {
        $expensesClaimTypeLanguage = factory(ExpensesClaimTypeLanguage::class)->make()->toArray();

        $createdExpensesClaimTypeLanguage = $this->expensesClaimTypeLanguageRepo->create($expensesClaimTypeLanguage);

        $createdExpensesClaimTypeLanguage = $createdExpensesClaimTypeLanguage->toArray();
        $this->assertArrayHasKey('id', $createdExpensesClaimTypeLanguage);
        $this->assertNotNull($createdExpensesClaimTypeLanguage['id'], 'Created ExpensesClaimTypeLanguage must have id specified');
        $this->assertNotNull(ExpensesClaimTypeLanguage::find($createdExpensesClaimTypeLanguage['id']), 'ExpensesClaimTypeLanguage with given id must be in DB');
        $this->assertModelData($expensesClaimTypeLanguage, $createdExpensesClaimTypeLanguage);
    }

    /**
     * @test read
     */
    public function test_read_expenses_claim_type_language()
    {
        $expensesClaimTypeLanguage = factory(ExpensesClaimTypeLanguage::class)->create();

        $dbExpensesClaimTypeLanguage = $this->expensesClaimTypeLanguageRepo->find($expensesClaimTypeLanguage->id);

        $dbExpensesClaimTypeLanguage = $dbExpensesClaimTypeLanguage->toArray();
        $this->assertModelData($expensesClaimTypeLanguage->toArray(), $dbExpensesClaimTypeLanguage);
    }

    /**
     * @test update
     */
    public function test_update_expenses_claim_type_language()
    {
        $expensesClaimTypeLanguage = factory(ExpensesClaimTypeLanguage::class)->create();
        $fakeExpensesClaimTypeLanguage = factory(ExpensesClaimTypeLanguage::class)->make()->toArray();

        $updatedExpensesClaimTypeLanguage = $this->expensesClaimTypeLanguageRepo->update($fakeExpensesClaimTypeLanguage, $expensesClaimTypeLanguage->id);

        $this->assertModelData($fakeExpensesClaimTypeLanguage, $updatedExpensesClaimTypeLanguage->toArray());
        $dbExpensesClaimTypeLanguage = $this->expensesClaimTypeLanguageRepo->find($expensesClaimTypeLanguage->id);
        $this->assertModelData($fakeExpensesClaimTypeLanguage, $dbExpensesClaimTypeLanguage->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_expenses_claim_type_language()
    {
        $expensesClaimTypeLanguage = factory(ExpensesClaimTypeLanguage::class)->create();

        $resp = $this->expensesClaimTypeLanguageRepo->delete($expensesClaimTypeLanguage->id);

        $this->assertTrue($resp);
        $this->assertNull(ExpensesClaimTypeLanguage::find($expensesClaimTypeLanguage->id), 'ExpensesClaimTypeLanguage should not exist in DB');
    }
}
