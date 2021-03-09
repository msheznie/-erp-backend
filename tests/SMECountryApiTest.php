<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMECountry;

class SMECountryApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_country()
    {
        $sMECountry = factory(SMECountry::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_countries', $sMECountry
        );

        $this->assertApiResponse($sMECountry);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_country()
    {
        $sMECountry = factory(SMECountry::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_countries/'.$sMECountry->id
        );

        $this->assertApiResponse($sMECountry->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_country()
    {
        $sMECountry = factory(SMECountry::class)->create();
        $editedSMECountry = factory(SMECountry::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_countries/'.$sMECountry->id,
            $editedSMECountry
        );

        $this->assertApiResponse($editedSMECountry);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_country()
    {
        $sMECountry = factory(SMECountry::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_countries/'.$sMECountry->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_countries/'.$sMECountry->id
        );

        $this->response->assertStatus(404);
    }
}
