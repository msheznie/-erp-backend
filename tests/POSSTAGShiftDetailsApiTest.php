<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSTAGShiftDetails;

class POSSTAGShiftDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_s_t_a_g_shift_details()
    {
        $pOSSTAGShiftDetails = factory(POSSTAGShiftDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_s_t_a_g_shift_details', $pOSSTAGShiftDetails
        );

        $this->assertApiResponse($pOSSTAGShiftDetails);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_s_t_a_g_shift_details()
    {
        $pOSSTAGShiftDetails = factory(POSSTAGShiftDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_t_a_g_shift_details/'.$pOSSTAGShiftDetails->id
        );

        $this->assertApiResponse($pOSSTAGShiftDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_s_t_a_g_shift_details()
    {
        $pOSSTAGShiftDetails = factory(POSSTAGShiftDetails::class)->create();
        $editedPOSSTAGShiftDetails = factory(POSSTAGShiftDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_s_t_a_g_shift_details/'.$pOSSTAGShiftDetails->id,
            $editedPOSSTAGShiftDetails
        );

        $this->assertApiResponse($editedPOSSTAGShiftDetails);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_s_t_a_g_shift_details()
    {
        $pOSSTAGShiftDetails = factory(POSSTAGShiftDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_s_t_a_g_shift_details/'.$pOSSTAGShiftDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_t_a_g_shift_details/'.$pOSSTAGShiftDetails->id
        );

        $this->response->assertStatus(404);
    }
}
