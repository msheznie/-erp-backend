<?php namespace Tests\Repositories;

use App\Models\DashboardWidgetMaster;
use App\Repositories\DashboardWidgetMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeDashboardWidgetMasterTrait;
use Tests\ApiTestTrait;

class DashboardWidgetMasterRepositoryTest extends TestCase
{
    use MakeDashboardWidgetMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DashboardWidgetMasterRepository
     */
    protected $dashboardWidgetMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->dashboardWidgetMasterRepo = \App::make(DashboardWidgetMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_dashboard_widget_master()
    {
        $dashboardWidgetMaster = $this->fakeDashboardWidgetMasterData();
        $createdDashboardWidgetMaster = $this->dashboardWidgetMasterRepo->create($dashboardWidgetMaster);
        $createdDashboardWidgetMaster = $createdDashboardWidgetMaster->toArray();
        $this->assertArrayHasKey('id', $createdDashboardWidgetMaster);
        $this->assertNotNull($createdDashboardWidgetMaster['id'], 'Created DashboardWidgetMaster must have id specified');
        $this->assertNotNull(DashboardWidgetMaster::find($createdDashboardWidgetMaster['id']), 'DashboardWidgetMaster with given id must be in DB');
        $this->assertModelData($dashboardWidgetMaster, $createdDashboardWidgetMaster);
    }

    /**
     * @test read
     */
    public function test_read_dashboard_widget_master()
    {
        $dashboardWidgetMaster = $this->makeDashboardWidgetMaster();
        $dbDashboardWidgetMaster = $this->dashboardWidgetMasterRepo->find($dashboardWidgetMaster->id);
        $dbDashboardWidgetMaster = $dbDashboardWidgetMaster->toArray();
        $this->assertModelData($dashboardWidgetMaster->toArray(), $dbDashboardWidgetMaster);
    }

    /**
     * @test update
     */
    public function test_update_dashboard_widget_master()
    {
        $dashboardWidgetMaster = $this->makeDashboardWidgetMaster();
        $fakeDashboardWidgetMaster = $this->fakeDashboardWidgetMasterData();
        $updatedDashboardWidgetMaster = $this->dashboardWidgetMasterRepo->update($fakeDashboardWidgetMaster, $dashboardWidgetMaster->id);
        $this->assertModelData($fakeDashboardWidgetMaster, $updatedDashboardWidgetMaster->toArray());
        $dbDashboardWidgetMaster = $this->dashboardWidgetMasterRepo->find($dashboardWidgetMaster->id);
        $this->assertModelData($fakeDashboardWidgetMaster, $dbDashboardWidgetMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_dashboard_widget_master()
    {
        $dashboardWidgetMaster = $this->makeDashboardWidgetMaster();
        $resp = $this->dashboardWidgetMasterRepo->delete($dashboardWidgetMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(DashboardWidgetMaster::find($dashboardWidgetMaster->id), 'DashboardWidgetMaster should not exist in DB');
    }
}
