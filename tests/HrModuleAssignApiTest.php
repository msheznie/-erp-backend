<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HrModuleAssign;

class HrModuleAssignApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hr_module_assign()
    {
        $hrModuleAssign = factory(HrModuleAssign::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/hr_module_assigns', $hrModuleAssign
        );

        $this->assertApiResponse($hrModuleAssign);
    }

    /**
     * @test
     */
    public function test_read_hr_module_assign()
    {
        $hrModuleAssign = factory(HrModuleAssign::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/hr_module_assigns/'.$hrModuleAssign->id
        );

        $this->assertApiResponse($hrModuleAssign->toArray());
    }

    /**
     * @test
     */
    public function test_update_hr_module_assign()
    {
        $hrModuleAssign = factory(HrModuleAssign::class)->create();
        $editedHrModuleAssign = factory(HrModuleAssign::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/hr_module_assigns/'.$hrModuleAssign->id,
            $editedHrModuleAssign
        );

        $this->assertApiResponse($editedHrModuleAssign);
    }

    /**
     * @test
     */
    public function test_delete_hr_module_assign()
    {
        $hrModuleAssign = factory(HrModuleAssign::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/hr_module_assigns/'.$hrModuleAssign->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/hr_module_assigns/'.$hrModuleAssign->id
        );

        $this->response->assertStatus(404);
    }
}
