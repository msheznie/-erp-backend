<?php namespace Tests\Repositories;

use App\Models\ChartOfAccountAllocationDetailHistory;
use App\Repositories\ChartOfAccountAllocationDetailHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeChartOfAccountAllocationDetailHistoryTrait;
use Tests\ApiTestTrait;

class ChartOfAccountAllocationDetailHistoryRepositoryTest extends TestCase
{
    use MakeChartOfAccountAllocationDetailHistoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ChartOfAccountAllocationDetailHistoryRepository
     */
    protected $chartOfAccountAllocationDetailHistoryRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->chartOfAccountAllocationDetailHistoryRepo = \App::make(ChartOfAccountAllocationDetailHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_chart_of_account_allocation_detail_history()
    {
        $chartOfAccountAllocationDetailHistory = $this->fakeChartOfAccountAllocationDetailHistoryData();
        $createdChartOfAccountAllocationDetailHistory = $this->chartOfAccountAllocationDetailHistoryRepo->create($chartOfAccountAllocationDetailHistory);
        $createdChartOfAccountAllocationDetailHistory = $createdChartOfAccountAllocationDetailHistory->toArray();
        $this->assertArrayHasKey('id', $createdChartOfAccountAllocationDetailHistory);
        $this->assertNotNull($createdChartOfAccountAllocationDetailHistory['id'], 'Created ChartOfAccountAllocationDetailHistory must have id specified');
        $this->assertNotNull(ChartOfAccountAllocationDetailHistory::find($createdChartOfAccountAllocationDetailHistory['id']), 'ChartOfAccountAllocationDetailHistory with given id must be in DB');
        $this->assertModelData($chartOfAccountAllocationDetailHistory, $createdChartOfAccountAllocationDetailHistory);
    }

    /**
     * @test read
     */
    public function test_read_chart_of_account_allocation_detail_history()
    {
        $chartOfAccountAllocationDetailHistory = $this->makeChartOfAccountAllocationDetailHistory();
        $dbChartOfAccountAllocationDetailHistory = $this->chartOfAccountAllocationDetailHistoryRepo->find($chartOfAccountAllocationDetailHistory->id);
        $dbChartOfAccountAllocationDetailHistory = $dbChartOfAccountAllocationDetailHistory->toArray();
        $this->assertModelData($chartOfAccountAllocationDetailHistory->toArray(), $dbChartOfAccountAllocationDetailHistory);
    }

    /**
     * @test update
     */
    public function test_update_chart_of_account_allocation_detail_history()
    {
        $chartOfAccountAllocationDetailHistory = $this->makeChartOfAccountAllocationDetailHistory();
        $fakeChartOfAccountAllocationDetailHistory = $this->fakeChartOfAccountAllocationDetailHistoryData();
        $updatedChartOfAccountAllocationDetailHistory = $this->chartOfAccountAllocationDetailHistoryRepo->update($fakeChartOfAccountAllocationDetailHistory, $chartOfAccountAllocationDetailHistory->id);
        $this->assertModelData($fakeChartOfAccountAllocationDetailHistory, $updatedChartOfAccountAllocationDetailHistory->toArray());
        $dbChartOfAccountAllocationDetailHistory = $this->chartOfAccountAllocationDetailHistoryRepo->find($chartOfAccountAllocationDetailHistory->id);
        $this->assertModelData($fakeChartOfAccountAllocationDetailHistory, $dbChartOfAccountAllocationDetailHistory->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_chart_of_account_allocation_detail_history()
    {
        $chartOfAccountAllocationDetailHistory = $this->makeChartOfAccountAllocationDetailHistory();
        $resp = $this->chartOfAccountAllocationDetailHistoryRepo->delete($chartOfAccountAllocationDetailHistory->id);
        $this->assertTrue($resp);
        $this->assertNull(ChartOfAccountAllocationDetailHistory::find($chartOfAccountAllocationDetailHistory->id), 'ChartOfAccountAllocationDetailHistory should not exist in DB');
    }
}
