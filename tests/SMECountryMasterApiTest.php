<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMECountryMaster;

class SMECountryMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_country_master()
    {
        $sMECountryMaster = factory(SMECountryMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_country_masters', $sMECountryMaster
        );

        $this->assertApiResponse($sMECountryMaster);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_country_master()
    {
        $sMECountryMaster = factory(SMECountryMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_country_masters/'.$sMECountryMaster->id
        );

        $this->assertApiResponse($sMECountryMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_country_master()
    {
        $sMECountryMaster = factory(SMECountryMaster::class)->create();
        $editedSMECountryMaster = factory(SMECountryMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_country_masters/'.$sMECountryMaster->id,
            $editedSMECountryMaster
        );

        $this->assertApiResponse($editedSMECountryMaster);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_country_master()
    {
        $sMECountryMaster = factory(SMECountryMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_country_masters/'.$sMECountryMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_country_masters/'.$sMECountryMaster->id
        );

        $this->response->assertStatus(404);
    }
}
