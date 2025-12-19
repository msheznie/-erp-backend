<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeChartOfAccountAllocationMasterTrait;
use Tests\ApiTestTrait;

class ChartOfAccountAllocationMasterApiTest extends TestCase
{
    use MakeChartOfAccountAllocationMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_chart_of_account_allocation_master()
    {
        $chartOfAccountAllocationMaster = $this->fakeChartOfAccountAllocationMasterData();
        $this->response = $this->json('POST', '/api/chartOfAccountAllocationMasters', $chartOfAccountAllocationMaster);

        $this->assertApiResponse($chartOfAccountAllocationMaster);
    }

    /**
     * @test
     */
    public function test_read_chart_of_account_allocation_master()
    {
        $chartOfAccountAllocationMaster = $this->makeChartOfAccountAllocationMaster();
        $this->response = $this->json('GET', '/api/chartOfAccountAllocationMasters/'.$chartOfAccountAllocationMaster->id);

        $this->assertApiResponse($chartOfAccountAllocationMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_chart_of_account_allocation_master()
    {
        $chartOfAccountAllocationMaster = $this->makeChartOfAccountAllocationMaster();
        $editedChartOfAccountAllocationMaster = $this->fakeChartOfAccountAllocationMasterData();

        $this->response = $this->json('PUT', '/api/chartOfAccountAllocationMasters/'.$chartOfAccountAllocationMaster->id, $editedChartOfAccountAllocationMaster);

        $this->assertApiResponse($editedChartOfAccountAllocationMaster);
    }

    /**
     * @test
     */
    public function test_delete_chart_of_account_allocation_master()
    {
        $chartOfAccountAllocationMaster = $this->makeChartOfAccountAllocationMaster();
        $this->response = $this->json('DELETE', '/api/chartOfAccountAllocationMasters/'.$chartOfAccountAllocationMaster->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/chartOfAccountAllocationMasters/'.$chartOfAccountAllocationMaster->id);

        $this->response->assertStatus(404);
    }
}
