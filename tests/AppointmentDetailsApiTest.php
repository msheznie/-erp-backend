<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AppointmentDetails;

class AppointmentDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_appointment_details()
    {
        $appointmentDetails = factory(AppointmentDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/appointment_details', $appointmentDetails
        );

        $this->assertApiResponse($appointmentDetails);
    }

    /**
     * @test
     */
    public function test_read_appointment_details()
    {
        $appointmentDetails = factory(AppointmentDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/appointment_details/'.$appointmentDetails->id
        );

        $this->assertApiResponse($appointmentDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_appointment_details()
    {
        $appointmentDetails = factory(AppointmentDetails::class)->create();
        $editedAppointmentDetails = factory(AppointmentDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/appointment_details/'.$appointmentDetails->id,
            $editedAppointmentDetails
        );

        $this->assertApiResponse($editedAppointmentDetails);
    }

    /**
     * @test
     */
    public function test_delete_appointment_details()
    {
        $appointmentDetails = factory(AppointmentDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/appointment_details/'.$appointmentDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/appointment_details/'.$appointmentDetails->id
        );

        $this->response->assertStatus(404);
    }
}
