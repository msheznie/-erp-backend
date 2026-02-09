<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SRMScenarioMaster;

class SRMScenarioMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_r_m_scenario_master()
    {
        $sRMScenarioMaster = factory(SRMScenarioMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_r_m_scenario_masters', $sRMScenarioMaster
        );

        $this->assertApiResponse($sRMScenarioMaster);
    }

    /**
     * @test
     */
    public function test_read_s_r_m_scenario_master()
    {
        $sRMScenarioMaster = factory(SRMScenarioMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_r_m_scenario_masters/'.$sRMScenarioMaster->id
        );

        $this->assertApiResponse($sRMScenarioMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_r_m_scenario_master()
    {
        $sRMScenarioMaster = factory(SRMScenarioMaster::class)->create();
        $editedSRMScenarioMaster = factory(SRMScenarioMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_r_m_scenario_masters/'.$sRMScenarioMaster->id,
            $editedSRMScenarioMaster
        );

        $this->assertApiResponse($editedSRMScenarioMaster);
    }

    /**
     * @test
     */
    public function test_delete_s_r_m_scenario_master()
    {
        $sRMScenarioMaster = factory(SRMScenarioMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_r_m_scenario_masters/'.$sRMScenarioMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_r_m_scenario_masters/'.$sRMScenarioMaster->id
        );

        $this->response->assertStatus(404);
    }
}
