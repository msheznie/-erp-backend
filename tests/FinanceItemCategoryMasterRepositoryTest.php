<?php

use App\Models\FinanceItemCategoryMaster;
use App\Repositories\FinanceItemCategoryMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FinanceItemCategoryMasterRepositoryTest extends TestCase
{
    use MakeFinanceItemCategoryMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FinanceItemCategoryMasterRepository
     */
    protected $financeItemCategoryMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->financeItemCategoryMasterRepo = App::make(FinanceItemCategoryMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFinanceItemCategoryMaster()
    {
        $financeItemCategoryMaster = $this->fakeFinanceItemCategoryMasterData();
        $createdFinanceItemCategoryMaster = $this->financeItemCategoryMasterRepo->create($financeItemCategoryMaster);
        $createdFinanceItemCategoryMaster = $createdFinanceItemCategoryMaster->toArray();
        $this->assertArrayHasKey('id', $createdFinanceItemCategoryMaster);
        $this->assertNotNull($createdFinanceItemCategoryMaster['id'], 'Created FinanceItemCategoryMaster must have id specified');
        $this->assertNotNull(FinanceItemCategoryMaster::find($createdFinanceItemCategoryMaster['id']), 'FinanceItemCategoryMaster with given id must be in DB');
        $this->assertModelData($financeItemCategoryMaster, $createdFinanceItemCategoryMaster);
    }

    /**
     * @test read
     */
    public function testReadFinanceItemCategoryMaster()
    {
        $financeItemCategoryMaster = $this->makeFinanceItemCategoryMaster();
        $dbFinanceItemCategoryMaster = $this->financeItemCategoryMasterRepo->find($financeItemCategoryMaster->id);
        $dbFinanceItemCategoryMaster = $dbFinanceItemCategoryMaster->toArray();
        $this->assertModelData($financeItemCategoryMaster->toArray(), $dbFinanceItemCategoryMaster);
    }

    /**
     * @test update
     */
    public function testUpdateFinanceItemCategoryMaster()
    {
        $financeItemCategoryMaster = $this->makeFinanceItemCategoryMaster();
        $fakeFinanceItemCategoryMaster = $this->fakeFinanceItemCategoryMasterData();
        $updatedFinanceItemCategoryMaster = $this->financeItemCategoryMasterRepo->update($fakeFinanceItemCategoryMaster, $financeItemCategoryMaster->id);
        $this->assertModelData($fakeFinanceItemCategoryMaster, $updatedFinanceItemCategoryMaster->toArray());
        $dbFinanceItemCategoryMaster = $this->financeItemCategoryMasterRepo->find($financeItemCategoryMaster->id);
        $this->assertModelData($fakeFinanceItemCategoryMaster, $dbFinanceItemCategoryMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFinanceItemCategoryMaster()
    {
        $financeItemCategoryMaster = $this->makeFinanceItemCategoryMaster();
        $resp = $this->financeItemCategoryMasterRepo->delete($financeItemCategoryMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(FinanceItemCategoryMaster::find($financeItemCategoryMaster->id), 'FinanceItemCategoryMaster should not exist in DB');
    }
}
