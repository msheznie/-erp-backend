<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SrmPOAcknowledgement;

class SrmPOAcknowledgementApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_srm_p_o_acknowledgement()
    {
        $srmPOAcknowledgement = factory(SrmPOAcknowledgement::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/srm_p_o_acknowledgements', $srmPOAcknowledgement
        );

        $this->assertApiResponse($srmPOAcknowledgement);
    }

    /**
     * @test
     */
    public function test_read_srm_p_o_acknowledgement()
    {
        $srmPOAcknowledgement = factory(SrmPOAcknowledgement::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/srm_p_o_acknowledgements/'.$srmPOAcknowledgement->id
        );

        $this->assertApiResponse($srmPOAcknowledgement->toArray());
    }

    /**
     * @test
     */
    public function test_update_srm_p_o_acknowledgement()
    {
        $srmPOAcknowledgement = factory(SrmPOAcknowledgement::class)->create();
        $editedSrmPOAcknowledgement = factory(SrmPOAcknowledgement::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/srm_p_o_acknowledgements/'.$srmPOAcknowledgement->id,
            $editedSrmPOAcknowledgement
        );

        $this->assertApiResponse($editedSrmPOAcknowledgement);
    }

    /**
     * @test
     */
    public function test_delete_srm_p_o_acknowledgement()
    {
        $srmPOAcknowledgement = factory(SrmPOAcknowledgement::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/srm_p_o_acknowledgements/'.$srmPOAcknowledgement->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/srm_p_o_acknowledgements/'.$srmPOAcknowledgement->id
        );

        $this->response->assertStatus(404);
    }
}
