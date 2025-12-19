<?php namespace Tests\Repositories;

use App\Models\ChartOfAccountAllocationMaster;
use App\Repositories\ChartOfAccountAllocationMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeChartOfAccountAllocationMasterTrait;
use Tests\ApiTestTrait;

class ChartOfAccountAllocationMasterRepositoryTest extends TestCase
{
    use MakeChartOfAccountAllocationMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ChartOfAccountAllocationMasterRepository
     */
    protected $chartOfAccountAllocationMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->chartOfAccountAllocationMasterRepo = \App::make(ChartOfAccountAllocationMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_chart_of_account_allocation_master()
    {
        $chartOfAccountAllocationMaster = $this->fakeChartOfAccountAllocationMasterData();
        $createdChartOfAccountAllocationMaster = $this->chartOfAccountAllocationMasterRepo->create($chartOfAccountAllocationMaster);
        $createdChartOfAccountAllocationMaster = $createdChartOfAccountAllocationMaster->toArray();
        $this->assertArrayHasKey('id', $createdChartOfAccountAllocationMaster);
        $this->assertNotNull($createdChartOfAccountAllocationMaster['id'], 'Created ChartOfAccountAllocationMaster must have id specified');
        $this->assertNotNull(ChartOfAccountAllocationMaster::find($createdChartOfAccountAllocationMaster['id']), 'ChartOfAccountAllocationMaster with given id must be in DB');
        $this->assertModelData($chartOfAccountAllocationMaster, $createdChartOfAccountAllocationMaster);
    }

    /**
     * @test read
     */
    public function test_read_chart_of_account_allocation_master()
    {
        $chartOfAccountAllocationMaster = $this->makeChartOfAccountAllocationMaster();
        $dbChartOfAccountAllocationMaster = $this->chartOfAccountAllocationMasterRepo->find($chartOfAccountAllocationMaster->id);
        $dbChartOfAccountAllocationMaster = $dbChartOfAccountAllocationMaster->toArray();
        $this->assertModelData($chartOfAccountAllocationMaster->toArray(), $dbChartOfAccountAllocationMaster);
    }

    /**
     * @test update
     */
    public function test_update_chart_of_account_allocation_master()
    {
        $chartOfAccountAllocationMaster = $this->makeChartOfAccountAllocationMaster();
        $fakeChartOfAccountAllocationMaster = $this->fakeChartOfAccountAllocationMasterData();
        $updatedChartOfAccountAllocationMaster = $this->chartOfAccountAllocationMasterRepo->update($fakeChartOfAccountAllocationMaster, $chartOfAccountAllocationMaster->id);
        $this->assertModelData($fakeChartOfAccountAllocationMaster, $updatedChartOfAccountAllocationMaster->toArray());
        $dbChartOfAccountAllocationMaster = $this->chartOfAccountAllocationMasterRepo->find($chartOfAccountAllocationMaster->id);
        $this->assertModelData($fakeChartOfAccountAllocationMaster, $dbChartOfAccountAllocationMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_chart_of_account_allocation_master()
    {
        $chartOfAccountAllocationMaster = $this->makeChartOfAccountAllocationMaster();
        $resp = $this->chartOfAccountAllocationMasterRepo->delete($chartOfAccountAllocationMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(ChartOfAccountAllocationMaster::find($chartOfAccountAllocationMaster->id), 'ChartOfAccountAllocationMaster should not exist in DB');
    }
}
