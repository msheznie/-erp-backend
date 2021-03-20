<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMEReligion;

class SMEReligionApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_religion()
    {
        $sMEReligion = factory(SMEReligion::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_religions', $sMEReligion
        );

        $this->assertApiResponse($sMEReligion);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_religion()
    {
        $sMEReligion = factory(SMEReligion::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_religions/'.$sMEReligion->id
        );

        $this->assertApiResponse($sMEReligion->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_religion()
    {
        $sMEReligion = factory(SMEReligion::class)->create();
        $editedSMEReligion = factory(SMEReligion::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_religions/'.$sMEReligion->id,
            $editedSMEReligion
        );

        $this->assertApiResponse($editedSMEReligion);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_religion()
    {
        $sMEReligion = factory(SMEReligion::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_religions/'.$sMEReligion->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_religions/'.$sMEReligion->id
        );

        $this->response->assertStatus(404);
    }
}
