<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\IOUBookingMaster;

class IOUBookingMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_i_o_u_booking_master()
    {
        $iOUBookingMaster = factory(IOUBookingMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/i_o_u_booking_masters', $iOUBookingMaster
        );

        $this->assertApiResponse($iOUBookingMaster);
    }

    /**
     * @test
     */
    public function test_read_i_o_u_booking_master()
    {
        $iOUBookingMaster = factory(IOUBookingMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/i_o_u_booking_masters/'.$iOUBookingMaster->id
        );

        $this->assertApiResponse($iOUBookingMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_i_o_u_booking_master()
    {
        $iOUBookingMaster = factory(IOUBookingMaster::class)->create();
        $editedIOUBookingMaster = factory(IOUBookingMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/i_o_u_booking_masters/'.$iOUBookingMaster->id,
            $editedIOUBookingMaster
        );

        $this->assertApiResponse($editedIOUBookingMaster);
    }

    /**
     * @test
     */
    public function test_delete_i_o_u_booking_master()
    {
        $iOUBookingMaster = factory(IOUBookingMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/i_o_u_booking_masters/'.$iOUBookingMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/i_o_u_booking_masters/'.$iOUBookingMaster->id
        );

        $this->response->assertStatus(404);
    }
}
