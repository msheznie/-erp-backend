<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMENationality;

class SMENationalityApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_nationality()
    {
        $sMENationality = factory(SMENationality::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_nationalities', $sMENationality
        );

        $this->assertApiResponse($sMENationality);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_nationality()
    {
        $sMENationality = factory(SMENationality::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_nationalities/'.$sMENationality->id
        );

        $this->assertApiResponse($sMENationality->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_nationality()
    {
        $sMENationality = factory(SMENationality::class)->create();
        $editedSMENationality = factory(SMENationality::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_nationalities/'.$sMENationality->id,
            $editedSMENationality
        );

        $this->assertApiResponse($editedSMENationality);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_nationality()
    {
        $sMENationality = factory(SMENationality::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_nationalities/'.$sMENationality->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_nationalities/'.$sMENationality->id
        );

        $this->response->assertStatus(404);
    }
}
