<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ERPLanguageMaster;

class ERPLanguageMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_e_r_p_language_master()
    {
        $eRPLanguageMaster = factory(ERPLanguageMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/e_r_p_language_masters', $eRPLanguageMaster
        );

        $this->assertApiResponse($eRPLanguageMaster);
    }

    /**
     * @test
     */
    public function test_read_e_r_p_language_master()
    {
        $eRPLanguageMaster = factory(ERPLanguageMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/e_r_p_language_masters/'.$eRPLanguageMaster->id
        );

        $this->assertApiResponse($eRPLanguageMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_e_r_p_language_master()
    {
        $eRPLanguageMaster = factory(ERPLanguageMaster::class)->create();
        $editedERPLanguageMaster = factory(ERPLanguageMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/e_r_p_language_masters/'.$eRPLanguageMaster->id,
            $editedERPLanguageMaster
        );

        $this->assertApiResponse($editedERPLanguageMaster);
    }

    /**
     * @test
     */
    public function test_delete_e_r_p_language_master()
    {
        $eRPLanguageMaster = factory(ERPLanguageMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/e_r_p_language_masters/'.$eRPLanguageMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/e_r_p_language_masters/'.$eRPLanguageMaster->id
        );

        $this->response->assertStatus(404);
    }
}
