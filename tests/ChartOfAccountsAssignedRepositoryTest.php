<?php

use App\Models\ChartOfAccountsAssigned;
use App\Repositories\ChartOfAccountsAssignedRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChartOfAccountsAssignedRepositoryTest extends TestCase
{
    use MakeChartOfAccountsAssignedTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ChartOfAccountsAssignedRepository
     */
    protected $chartOfAccountsAssignedRepo;

    public function setUp()
    {
        parent::setUp();
        $this->chartOfAccountsAssignedRepo = App::make(ChartOfAccountsAssignedRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateChartOfAccountsAssigned()
    {
        $chartOfAccountsAssigned = $this->fakeChartOfAccountsAssignedData();
        $createdChartOfAccountsAssigned = $this->chartOfAccountsAssignedRepo->create($chartOfAccountsAssigned);
        $createdChartOfAccountsAssigned = $createdChartOfAccountsAssigned->toArray();
        $this->assertArrayHasKey('id', $createdChartOfAccountsAssigned);
        $this->assertNotNull($createdChartOfAccountsAssigned['id'], 'Created ChartOfAccountsAssigned must have id specified');
        $this->assertNotNull(ChartOfAccountsAssigned::find($createdChartOfAccountsAssigned['id']), 'ChartOfAccountsAssigned with given id must be in DB');
        $this->assertModelData($chartOfAccountsAssigned, $createdChartOfAccountsAssigned);
    }

    /**
     * @test read
     */
    public function testReadChartOfAccountsAssigned()
    {
        $chartOfAccountsAssigned = $this->makeChartOfAccountsAssigned();
        $dbChartOfAccountsAssigned = $this->chartOfAccountsAssignedRepo->find($chartOfAccountsAssigned->id);
        $dbChartOfAccountsAssigned = $dbChartOfAccountsAssigned->toArray();
        $this->assertModelData($chartOfAccountsAssigned->toArray(), $dbChartOfAccountsAssigned);
    }

    /**
     * @test update
     */
    public function testUpdateChartOfAccountsAssigned()
    {
        $chartOfAccountsAssigned = $this->makeChartOfAccountsAssigned();
        $fakeChartOfAccountsAssigned = $this->fakeChartOfAccountsAssignedData();
        $updatedChartOfAccountsAssigned = $this->chartOfAccountsAssignedRepo->update($fakeChartOfAccountsAssigned, $chartOfAccountsAssigned->id);
        $this->assertModelData($fakeChartOfAccountsAssigned, $updatedChartOfAccountsAssigned->toArray());
        $dbChartOfAccountsAssigned = $this->chartOfAccountsAssignedRepo->find($chartOfAccountsAssigned->id);
        $this->assertModelData($fakeChartOfAccountsAssigned, $dbChartOfAccountsAssigned->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteChartOfAccountsAssigned()
    {
        $chartOfAccountsAssigned = $this->makeChartOfAccountsAssigned();
        $resp = $this->chartOfAccountsAssignedRepo->delete($chartOfAccountsAssigned->id);
        $this->assertTrue($resp);
        $this->assertNull(ChartOfAccountsAssigned::find($chartOfAccountsAssigned->id), 'ChartOfAccountsAssigned should not exist in DB');
    }
}
