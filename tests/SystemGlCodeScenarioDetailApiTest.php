<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SystemGlCodeScenarioDetail;

class SystemGlCodeScenarioDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_system_gl_code_scenario_detail()
    {
        $systemGlCodeScenarioDetail = factory(SystemGlCodeScenarioDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/system_gl_code_scenario_details', $systemGlCodeScenarioDetail
        );

        $this->assertApiResponse($systemGlCodeScenarioDetail);
    }

    /**
     * @test
     */
    public function test_read_system_gl_code_scenario_detail()
    {
        $systemGlCodeScenarioDetail = factory(SystemGlCodeScenarioDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/system_gl_code_scenario_details/'.$systemGlCodeScenarioDetail->id
        );

        $this->assertApiResponse($systemGlCodeScenarioDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_system_gl_code_scenario_detail()
    {
        $systemGlCodeScenarioDetail = factory(SystemGlCodeScenarioDetail::class)->create();
        $editedSystemGlCodeScenarioDetail = factory(SystemGlCodeScenarioDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/system_gl_code_scenario_details/'.$systemGlCodeScenarioDetail->id,
            $editedSystemGlCodeScenarioDetail
        );

        $this->assertApiResponse($editedSystemGlCodeScenarioDetail);
    }

    /**
     * @test
     */
    public function test_delete_system_gl_code_scenario_detail()
    {
        $systemGlCodeScenarioDetail = factory(SystemGlCodeScenarioDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/system_gl_code_scenario_details/'.$systemGlCodeScenarioDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/system_gl_code_scenario_details/'.$systemGlCodeScenarioDetail->id
        );

        $this->response->assertStatus(404);
    }
}
