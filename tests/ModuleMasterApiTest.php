<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ModuleMaster;

class ModuleMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_module_master()
    {
        $moduleMaster = factory(ModuleMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/module_masters', $moduleMaster
        );

        $this->assertApiResponse($moduleMaster);
    }

    /**
     * @test
     */
    public function test_read_module_master()
    {
        $moduleMaster = factory(ModuleMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/module_masters/'.$moduleMaster->id
        );

        $this->assertApiResponse($moduleMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_module_master()
    {
        $moduleMaster = factory(ModuleMaster::class)->create();
        $editedModuleMaster = factory(ModuleMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/module_masters/'.$moduleMaster->id,
            $editedModuleMaster
        );

        $this->assertApiResponse($editedModuleMaster);
    }

    /**
     * @test
     */
    public function test_delete_module_master()
    {
        $moduleMaster = factory(ModuleMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/module_masters/'.$moduleMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/module_masters/'.$moduleMaster->id
        );

        $this->response->assertStatus(404);
    }
}
