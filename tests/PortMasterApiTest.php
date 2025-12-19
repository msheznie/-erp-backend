<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PortMaster;

class PortMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_port_master()
    {
        $portMaster = factory(PortMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/port_masters', $portMaster
        );

        $this->assertApiResponse($portMaster);
    }

    /**
     * @test
     */
    public function test_read_port_master()
    {
        $portMaster = factory(PortMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/port_masters/'.$portMaster->id
        );

        $this->assertApiResponse($portMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_port_master()
    {
        $portMaster = factory(PortMaster::class)->create();
        $editedPortMaster = factory(PortMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/port_masters/'.$portMaster->id,
            $editedPortMaster
        );

        $this->assertApiResponse($editedPortMaster);
    }

    /**
     * @test
     */
    public function test_delete_port_master()
    {
        $portMaster = factory(PortMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/port_masters/'.$portMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/port_masters/'.$portMaster->id
        );

        $this->response->assertStatus(404);
    }
}
