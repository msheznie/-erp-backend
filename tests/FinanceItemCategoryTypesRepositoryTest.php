<?php namespace Tests\Repositories;

use App\Models\FinanceItemCategoryTypes;
use App\Repositories\FinanceItemCategoryTypesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class FinanceItemCategoryTypesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var FinanceItemCategoryTypesRepository
     */
    protected $financeItemCategoryTypesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->financeItemCategoryTypesRepo = \App::make(FinanceItemCategoryTypesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_finance_item_category_types()
    {
        $financeItemCategoryTypes = factory(FinanceItemCategoryTypes::class)->make()->toArray();

        $createdFinanceItemCategoryTypes = $this->financeItemCategoryTypesRepo->create($financeItemCategoryTypes);

        $createdFinanceItemCategoryTypes = $createdFinanceItemCategoryTypes->toArray();
        $this->assertArrayHasKey('id', $createdFinanceItemCategoryTypes);
        $this->assertNotNull($createdFinanceItemCategoryTypes['id'], 'Created FinanceItemCategoryTypes must have id specified');
        $this->assertNotNull(FinanceItemCategoryTypes::find($createdFinanceItemCategoryTypes['id']), 'FinanceItemCategoryTypes with given id must be in DB');
        $this->assertModelData($financeItemCategoryTypes, $createdFinanceItemCategoryTypes);
    }

    /**
     * @test read
     */
    public function test_read_finance_item_category_types()
    {
        $financeItemCategoryTypes = factory(FinanceItemCategoryTypes::class)->create();

        $dbFinanceItemCategoryTypes = $this->financeItemCategoryTypesRepo->find($financeItemCategoryTypes->id);

        $dbFinanceItemCategoryTypes = $dbFinanceItemCategoryTypes->toArray();
        $this->assertModelData($financeItemCategoryTypes->toArray(), $dbFinanceItemCategoryTypes);
    }

    /**
     * @test update
     */
    public function test_update_finance_item_category_types()
    {
        $financeItemCategoryTypes = factory(FinanceItemCategoryTypes::class)->create();
        $fakeFinanceItemCategoryTypes = factory(FinanceItemCategoryTypes::class)->make()->toArray();

        $updatedFinanceItemCategoryTypes = $this->financeItemCategoryTypesRepo->update($fakeFinanceItemCategoryTypes, $financeItemCategoryTypes->id);

        $this->assertModelData($fakeFinanceItemCategoryTypes, $updatedFinanceItemCategoryTypes->toArray());
        $dbFinanceItemCategoryTypes = $this->financeItemCategoryTypesRepo->find($financeItemCategoryTypes->id);
        $this->assertModelData($fakeFinanceItemCategoryTypes, $dbFinanceItemCategoryTypes->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_finance_item_category_types()
    {
        $financeItemCategoryTypes = factory(FinanceItemCategoryTypes::class)->create();

        $resp = $this->financeItemCategoryTypesRepo->delete($financeItemCategoryTypes->id);

        $this->assertTrue($resp);
        $this->assertNull(FinanceItemCategoryTypes::find($financeItemCategoryTypes->id), 'FinanceItemCategoryTypes should not exist in DB');
    }
}
