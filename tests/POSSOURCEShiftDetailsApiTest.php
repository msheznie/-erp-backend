<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSOURCEShiftDetails;

class POSSOURCEShiftDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_s_o_u_r_c_e_shift_details()
    {
        $pOSSOURCEShiftDetails = factory(POSSOURCEShiftDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_s_o_u_r_c_e_shift_details', $pOSSOURCEShiftDetails
        );

        $this->assertApiResponse($pOSSOURCEShiftDetails);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_s_o_u_r_c_e_shift_details()
    {
        $pOSSOURCEShiftDetails = factory(POSSOURCEShiftDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_o_u_r_c_e_shift_details/'.$pOSSOURCEShiftDetails->id
        );

        $this->assertApiResponse($pOSSOURCEShiftDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_s_o_u_r_c_e_shift_details()
    {
        $pOSSOURCEShiftDetails = factory(POSSOURCEShiftDetails::class)->create();
        $editedPOSSOURCEShiftDetails = factory(POSSOURCEShiftDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_s_o_u_r_c_e_shift_details/'.$pOSSOURCEShiftDetails->id,
            $editedPOSSOURCEShiftDetails
        );

        $this->assertApiResponse($editedPOSSOURCEShiftDetails);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_s_o_u_r_c_e_shift_details()
    {
        $pOSSOURCEShiftDetails = factory(POSSOURCEShiftDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_s_o_u_r_c_e_shift_details/'.$pOSSOURCEShiftDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_o_u_r_c_e_shift_details/'.$pOSSOURCEShiftDetails->id
        );

        $this->response->assertStatus(404);
    }
}
