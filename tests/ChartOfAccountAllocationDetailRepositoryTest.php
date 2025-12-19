<?php namespace Tests\Repositories;

use App\Models\ChartOfAccountAllocationDetail;
use App\Repositories\ChartOfAccountAllocationDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeChartOfAccountAllocationDetailTrait;
use Tests\ApiTestTrait;

class ChartOfAccountAllocationDetailRepositoryTest extends TestCase
{
    use MakeChartOfAccountAllocationDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ChartOfAccountAllocationDetailRepository
     */
    protected $chartOfAccountAllocationDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->chartOfAccountAllocationDetailRepo = \App::make(ChartOfAccountAllocationDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_chart_of_account_allocation_detail()
    {
        $chartOfAccountAllocationDetail = $this->fakeChartOfAccountAllocationDetailData();
        $createdChartOfAccountAllocationDetail = $this->chartOfAccountAllocationDetailRepo->create($chartOfAccountAllocationDetail);
        $createdChartOfAccountAllocationDetail = $createdChartOfAccountAllocationDetail->toArray();
        $this->assertArrayHasKey('id', $createdChartOfAccountAllocationDetail);
        $this->assertNotNull($createdChartOfAccountAllocationDetail['id'], 'Created ChartOfAccountAllocationDetail must have id specified');
        $this->assertNotNull(ChartOfAccountAllocationDetail::find($createdChartOfAccountAllocationDetail['id']), 'ChartOfAccountAllocationDetail with given id must be in DB');
        $this->assertModelData($chartOfAccountAllocationDetail, $createdChartOfAccountAllocationDetail);
    }

    /**
     * @test read
     */
    public function test_read_chart_of_account_allocation_detail()
    {
        $chartOfAccountAllocationDetail = $this->makeChartOfAccountAllocationDetail();
        $dbChartOfAccountAllocationDetail = $this->chartOfAccountAllocationDetailRepo->find($chartOfAccountAllocationDetail->id);
        $dbChartOfAccountAllocationDetail = $dbChartOfAccountAllocationDetail->toArray();
        $this->assertModelData($chartOfAccountAllocationDetail->toArray(), $dbChartOfAccountAllocationDetail);
    }

    /**
     * @test update
     */
    public function test_update_chart_of_account_allocation_detail()
    {
        $chartOfAccountAllocationDetail = $this->makeChartOfAccountAllocationDetail();
        $fakeChartOfAccountAllocationDetail = $this->fakeChartOfAccountAllocationDetailData();
        $updatedChartOfAccountAllocationDetail = $this->chartOfAccountAllocationDetailRepo->update($fakeChartOfAccountAllocationDetail, $chartOfAccountAllocationDetail->id);
        $this->assertModelData($fakeChartOfAccountAllocationDetail, $updatedChartOfAccountAllocationDetail->toArray());
        $dbChartOfAccountAllocationDetail = $this->chartOfAccountAllocationDetailRepo->find($chartOfAccountAllocationDetail->id);
        $this->assertModelData($fakeChartOfAccountAllocationDetail, $dbChartOfAccountAllocationDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_chart_of_account_allocation_detail()
    {
        $chartOfAccountAllocationDetail = $this->makeChartOfAccountAllocationDetail();
        $resp = $this->chartOfAccountAllocationDetailRepo->delete($chartOfAccountAllocationDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(ChartOfAccountAllocationDetail::find($chartOfAccountAllocationDetail->id), 'ChartOfAccountAllocationDetail should not exist in DB');
    }
}
