<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeChartOfAccountAllocationDetailHistoryTrait;
use Tests\ApiTestTrait;

class ChartOfAccountAllocationDetailHistoryApiTest extends TestCase
{
    use MakeChartOfAccountAllocationDetailHistoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_chart_of_account_allocation_detail_history()
    {
        $chartOfAccountAllocationDetailHistory = $this->fakeChartOfAccountAllocationDetailHistoryData();
        $this->response = $this->json('POST', '/api/chartOfAccountAllocationDetailHistories', $chartOfAccountAllocationDetailHistory);

        $this->assertApiResponse($chartOfAccountAllocationDetailHistory);
    }

    /**
     * @test
     */
    public function test_read_chart_of_account_allocation_detail_history()
    {
        $chartOfAccountAllocationDetailHistory = $this->makeChartOfAccountAllocationDetailHistory();
        $this->response = $this->json('GET', '/api/chartOfAccountAllocationDetailHistories/'.$chartOfAccountAllocationDetailHistory->id);

        $this->assertApiResponse($chartOfAccountAllocationDetailHistory->toArray());
    }

    /**
     * @test
     */
    public function test_update_chart_of_account_allocation_detail_history()
    {
        $chartOfAccountAllocationDetailHistory = $this->makeChartOfAccountAllocationDetailHistory();
        $editedChartOfAccountAllocationDetailHistory = $this->fakeChartOfAccountAllocationDetailHistoryData();

        $this->response = $this->json('PUT', '/api/chartOfAccountAllocationDetailHistories/'.$chartOfAccountAllocationDetailHistory->id, $editedChartOfAccountAllocationDetailHistory);

        $this->assertApiResponse($editedChartOfAccountAllocationDetailHistory);
    }

    /**
     * @test
     */
    public function test_delete_chart_of_account_allocation_detail_history()
    {
        $chartOfAccountAllocationDetailHistory = $this->makeChartOfAccountAllocationDetailHistory();
        $this->response = $this->json('DELETE', '/api/chartOfAccountAllocationDetailHistories/'.$chartOfAccountAllocationDetailHistory->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/chartOfAccountAllocationDetailHistories/'.$chartOfAccountAllocationDetailHistory->id);

        $this->response->assertStatus(404);
    }
}
