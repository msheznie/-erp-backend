<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SystemGlCodeScenario;

class SystemGlCodeScenarioApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_system_gl_code_scenario()
    {
        $systemGlCodeScenario = factory(SystemGlCodeScenario::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/system_gl_code_scenarios', $systemGlCodeScenario
        );

        $this->assertApiResponse($systemGlCodeScenario);
    }

    /**
     * @test
     */
    public function test_read_system_gl_code_scenario()
    {
        $systemGlCodeScenario = factory(SystemGlCodeScenario::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/system_gl_code_scenarios/'.$systemGlCodeScenario->id
        );

        $this->assertApiResponse($systemGlCodeScenario->toArray());
    }

    /**
     * @test
     */
    public function test_update_system_gl_code_scenario()
    {
        $systemGlCodeScenario = factory(SystemGlCodeScenario::class)->create();
        $editedSystemGlCodeScenario = factory(SystemGlCodeScenario::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/system_gl_code_scenarios/'.$systemGlCodeScenario->id,
            $editedSystemGlCodeScenario
        );

        $this->assertApiResponse($editedSystemGlCodeScenario);
    }

    /**
     * @test
     */
    public function test_delete_system_gl_code_scenario()
    {
        $systemGlCodeScenario = factory(SystemGlCodeScenario::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/system_gl_code_scenarios/'.$systemGlCodeScenario->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/system_gl_code_scenarios/'.$systemGlCodeScenario->id
        );

        $this->response->assertStatus(404);
    }
}
