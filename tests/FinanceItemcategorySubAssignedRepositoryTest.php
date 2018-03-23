<?php

use App\Models\FinanceItemcategorySubAssigned;
use App\Repositories\FinanceItemcategorySubAssignedRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FinanceItemcategorySubAssignedRepositoryTest extends TestCase
{
    use MakeFinanceItemcategorySubAssignedTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FinanceItemcategorySubAssignedRepository
     */
    protected $financeItemcategorySubAssignedRepo;

    public function setUp()
    {
        parent::setUp();
        $this->financeItemcategorySubAssignedRepo = App::make(FinanceItemcategorySubAssignedRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFinanceItemcategorySubAssigned()
    {
        $financeItemcategorySubAssigned = $this->fakeFinanceItemcategorySubAssignedData();
        $createdFinanceItemcategorySubAssigned = $this->financeItemcategorySubAssignedRepo->create($financeItemcategorySubAssigned);
        $createdFinanceItemcategorySubAssigned = $createdFinanceItemcategorySubAssigned->toArray();
        $this->assertArrayHasKey('id', $createdFinanceItemcategorySubAssigned);
        $this->assertNotNull($createdFinanceItemcategorySubAssigned['id'], 'Created FinanceItemcategorySubAssigned must have id specified');
        $this->assertNotNull(FinanceItemcategorySubAssigned::find($createdFinanceItemcategorySubAssigned['id']), 'FinanceItemcategorySubAssigned with given id must be in DB');
        $this->assertModelData($financeItemcategorySubAssigned, $createdFinanceItemcategorySubAssigned);
    }

    /**
     * @test read
     */
    public function testReadFinanceItemcategorySubAssigned()
    {
        $financeItemcategorySubAssigned = $this->makeFinanceItemcategorySubAssigned();
        $dbFinanceItemcategorySubAssigned = $this->financeItemcategorySubAssignedRepo->find($financeItemcategorySubAssigned->id);
        $dbFinanceItemcategorySubAssigned = $dbFinanceItemcategorySubAssigned->toArray();
        $this->assertModelData($financeItemcategorySubAssigned->toArray(), $dbFinanceItemcategorySubAssigned);
    }

    /**
     * @test update
     */
    public function testUpdateFinanceItemcategorySubAssigned()
    {
        $financeItemcategorySubAssigned = $this->makeFinanceItemcategorySubAssigned();
        $fakeFinanceItemcategorySubAssigned = $this->fakeFinanceItemcategorySubAssignedData();
        $updatedFinanceItemcategorySubAssigned = $this->financeItemcategorySubAssignedRepo->update($fakeFinanceItemcategorySubAssigned, $financeItemcategorySubAssigned->id);
        $this->assertModelData($fakeFinanceItemcategorySubAssigned, $updatedFinanceItemcategorySubAssigned->toArray());
        $dbFinanceItemcategorySubAssigned = $this->financeItemcategorySubAssignedRepo->find($financeItemcategorySubAssigned->id);
        $this->assertModelData($fakeFinanceItemcategorySubAssigned, $dbFinanceItemcategorySubAssigned->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFinanceItemcategorySubAssigned()
    {
        $financeItemcategorySubAssigned = $this->makeFinanceItemcategorySubAssigned();
        $resp = $this->financeItemcategorySubAssignedRepo->delete($financeItemcategorySubAssigned->id);
        $this->assertTrue($resp);
        $this->assertNull(FinanceItemcategorySubAssigned::find($financeItemcategorySubAssigned->id), 'FinanceItemcategorySubAssigned should not exist in DB');
    }
}
