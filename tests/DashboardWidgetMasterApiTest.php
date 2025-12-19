<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeDashboardWidgetMasterTrait;
use Tests\ApiTestTrait;

class DashboardWidgetMasterApiTest extends TestCase
{
    use MakeDashboardWidgetMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_dashboard_widget_master()
    {
        $dashboardWidgetMaster = $this->fakeDashboardWidgetMasterData();
        $this->response = $this->json('POST', '/api/dashboardWidgetMasters', $dashboardWidgetMaster);

        $this->assertApiResponse($dashboardWidgetMaster);
    }

    /**
     * @test
     */
    public function test_read_dashboard_widget_master()
    {
        $dashboardWidgetMaster = $this->makeDashboardWidgetMaster();
        $this->response = $this->json('GET', '/api/dashboardWidgetMasters/'.$dashboardWidgetMaster->id);

        $this->assertApiResponse($dashboardWidgetMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_dashboard_widget_master()
    {
        $dashboardWidgetMaster = $this->makeDashboardWidgetMaster();
        $editedDashboardWidgetMaster = $this->fakeDashboardWidgetMasterData();

        $this->response = $this->json('PUT', '/api/dashboardWidgetMasters/'.$dashboardWidgetMaster->id, $editedDashboardWidgetMaster);

        $this->assertApiResponse($editedDashboardWidgetMaster);
    }

    /**
     * @test
     */
    public function test_delete_dashboard_widget_master()
    {
        $dashboardWidgetMaster = $this->makeDashboardWidgetMaster();
        $this->response = $this->json('DELETE', '/api/dashboardWidgetMasters/'.$dashboardWidgetMaster->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/dashboardWidgetMasters/'.$dashboardWidgetMaster->id);

        $this->response->assertStatus(404);
    }
}
