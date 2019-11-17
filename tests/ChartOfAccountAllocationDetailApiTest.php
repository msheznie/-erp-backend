<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeChartOfAccountAllocationDetailTrait;
use Tests\ApiTestTrait;

class ChartOfAccountAllocationDetailApiTest extends TestCase
{
    use MakeChartOfAccountAllocationDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_chart_of_account_allocation_detail()
    {
        $chartOfAccountAllocationDetail = $this->fakeChartOfAccountAllocationDetailData();
        $this->response = $this->json('POST', '/api/chartOfAccountAllocationDetails', $chartOfAccountAllocationDetail);

        $this->assertApiResponse($chartOfAccountAllocationDetail);
    }

    /**
     * @test
     */
    public function test_read_chart_of_account_allocation_detail()
    {
        $chartOfAccountAllocationDetail = $this->makeChartOfAccountAllocationDetail();
        $this->response = $this->json('GET', '/api/chartOfAccountAllocationDetails/'.$chartOfAccountAllocationDetail->id);

        $this->assertApiResponse($chartOfAccountAllocationDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_chart_of_account_allocation_detail()
    {
        $chartOfAccountAllocationDetail = $this->makeChartOfAccountAllocationDetail();
        $editedChartOfAccountAllocationDetail = $this->fakeChartOfAccountAllocationDetailData();

        $this->response = $this->json('PUT', '/api/chartOfAccountAllocationDetails/'.$chartOfAccountAllocationDetail->id, $editedChartOfAccountAllocationDetail);

        $this->assertApiResponse($editedChartOfAccountAllocationDetail);
    }

    /**
     * @test
     */
    public function test_delete_chart_of_account_allocation_detail()
    {
        $chartOfAccountAllocationDetail = $this->makeChartOfAccountAllocationDetail();
        $this->response = $this->json('DELETE', '/api/chartOfAccountAllocationDetails/'.$chartOfAccountAllocationDetail->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/chartOfAccountAllocationDetails/'.$chartOfAccountAllocationDetail->id);

        $this->response->assertStatus(404);
    }
}
