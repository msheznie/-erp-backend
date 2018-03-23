<?php

use App\Models\FinanceItemCategorySub;
use App\Repositories\FinanceItemCategorySubRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FinanceItemCategorySubRepositoryTest extends TestCase
{
    use MakeFinanceItemCategorySubTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FinanceItemCategorySubRepository
     */
    protected $financeItemCategorySubRepo;

    public function setUp()
    {
        parent::setUp();
        $this->financeItemCategorySubRepo = App::make(FinanceItemCategorySubRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFinanceItemCategorySub()
    {
        $financeItemCategorySub = $this->fakeFinanceItemCategorySubData();
        $createdFinanceItemCategorySub = $this->financeItemCategorySubRepo->create($financeItemCategorySub);
        $createdFinanceItemCategorySub = $createdFinanceItemCategorySub->toArray();
        $this->assertArrayHasKey('id', $createdFinanceItemCategorySub);
        $this->assertNotNull($createdFinanceItemCategorySub['id'], 'Created FinanceItemCategorySub must have id specified');
        $this->assertNotNull(FinanceItemCategorySub::find($createdFinanceItemCategorySub['id']), 'FinanceItemCategorySub with given id must be in DB');
        $this->assertModelData($financeItemCategorySub, $createdFinanceItemCategorySub);
    }

    /**
     * @test read
     */
    public function testReadFinanceItemCategorySub()
    {
        $financeItemCategorySub = $this->makeFinanceItemCategorySub();
        $dbFinanceItemCategorySub = $this->financeItemCategorySubRepo->find($financeItemCategorySub->id);
        $dbFinanceItemCategorySub = $dbFinanceItemCategorySub->toArray();
        $this->assertModelData($financeItemCategorySub->toArray(), $dbFinanceItemCategorySub);
    }

    /**
     * @test update
     */
    public function testUpdateFinanceItemCategorySub()
    {
        $financeItemCategorySub = $this->makeFinanceItemCategorySub();
        $fakeFinanceItemCategorySub = $this->fakeFinanceItemCategorySubData();
        $updatedFinanceItemCategorySub = $this->financeItemCategorySubRepo->update($fakeFinanceItemCategorySub, $financeItemCategorySub->id);
        $this->assertModelData($fakeFinanceItemCategorySub, $updatedFinanceItemCategorySub->toArray());
        $dbFinanceItemCategorySub = $this->financeItemCategorySubRepo->find($financeItemCategorySub->id);
        $this->assertModelData($fakeFinanceItemCategorySub, $dbFinanceItemCategorySub->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFinanceItemCategorySub()
    {
        $financeItemCategorySub = $this->makeFinanceItemCategorySub();
        $resp = $this->financeItemCategorySubRepo->delete($financeItemCategorySub->id);
        $this->assertTrue($resp);
        $this->assertNull(FinanceItemCategorySub::find($financeItemCategorySub->id), 'FinanceItemCategorySub should not exist in DB');
    }
}
