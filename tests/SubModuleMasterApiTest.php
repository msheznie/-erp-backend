<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SubModuleMaster;

class SubModuleMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_sub_module_master()
    {
        $subModuleMaster = factory(SubModuleMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/sub_module_masters', $subModuleMaster
        );

        $this->assertApiResponse($subModuleMaster);
    }

    /**
     * @test
     */
    public function test_read_sub_module_master()
    {
        $subModuleMaster = factory(SubModuleMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/sub_module_masters/'.$subModuleMaster->id
        );

        $this->assertApiResponse($subModuleMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_sub_module_master()
    {
        $subModuleMaster = factory(SubModuleMaster::class)->create();
        $editedSubModuleMaster = factory(SubModuleMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/sub_module_masters/'.$subModuleMaster->id,
            $editedSubModuleMaster
        );

        $this->assertApiResponse($editedSubModuleMaster);
    }

    /**
     * @test
     */
    public function test_delete_sub_module_master()
    {
        $subModuleMaster = factory(SubModuleMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/sub_module_masters/'.$subModuleMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/sub_module_masters/'.$subModuleMaster->id
        );

        $this->response->assertStatus(404);
    }
}
