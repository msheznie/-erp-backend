<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ModuleAssigned;

class ModuleAssignedApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_module_assigned()
    {
        $moduleAssigned = factory(ModuleAssigned::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/module_assigneds', $moduleAssigned
        );

        $this->assertApiResponse($moduleAssigned);
    }

    /**
     * @test
     */
    public function test_read_module_assigned()
    {
        $moduleAssigned = factory(ModuleAssigned::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/module_assigneds/'.$moduleAssigned->id
        );

        $this->assertApiResponse($moduleAssigned->toArray());
    }

    /**
     * @test
     */
    public function test_update_module_assigned()
    {
        $moduleAssigned = factory(ModuleAssigned::class)->create();
        $editedModuleAssigned = factory(ModuleAssigned::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/module_assigneds/'.$moduleAssigned->id,
            $editedModuleAssigned
        );

        $this->assertApiResponse($editedModuleAssigned);
    }

    /**
     * @test
     */
    public function test_delete_module_assigned()
    {
        $moduleAssigned = factory(ModuleAssigned::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/module_assigneds/'.$moduleAssigned->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/module_assigneds/'.$moduleAssigned->id
        );

        $this->response->assertStatus(404);
    }
}
