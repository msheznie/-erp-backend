<?php namespace Tests\Repositories;

use App\Models\AppointmentDetails;
use App\Repositories\AppointmentDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AppointmentDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AppointmentDetailsRepository
     */
    protected $appointmentDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->appointmentDetailsRepo = \App::make(AppointmentDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_appointment_details()
    {
        $appointmentDetails = factory(AppointmentDetails::class)->make()->toArray();

        $createdAppointmentDetails = $this->appointmentDetailsRepo->create($appointmentDetails);

        $createdAppointmentDetails = $createdAppointmentDetails->toArray();
        $this->assertArrayHasKey('id', $createdAppointmentDetails);
        $this->assertNotNull($createdAppointmentDetails['id'], 'Created AppointmentDetails must have id specified');
        $this->assertNotNull(AppointmentDetails::find($createdAppointmentDetails['id']), 'AppointmentDetails with given id must be in DB');
        $this->assertModelData($appointmentDetails, $createdAppointmentDetails);
    }

    /**
     * @test read
     */
    public function test_read_appointment_details()
    {
        $appointmentDetails = factory(AppointmentDetails::class)->create();

        $dbAppointmentDetails = $this->appointmentDetailsRepo->find($appointmentDetails->id);

        $dbAppointmentDetails = $dbAppointmentDetails->toArray();
        $this->assertModelData($appointmentDetails->toArray(), $dbAppointmentDetails);
    }

    /**
     * @test update
     */
    public function test_update_appointment_details()
    {
        $appointmentDetails = factory(AppointmentDetails::class)->create();
        $fakeAppointmentDetails = factory(AppointmentDetails::class)->make()->toArray();

        $updatedAppointmentDetails = $this->appointmentDetailsRepo->update($fakeAppointmentDetails, $appointmentDetails->id);

        $this->assertModelData($fakeAppointmentDetails, $updatedAppointmentDetails->toArray());
        $dbAppointmentDetails = $this->appointmentDetailsRepo->find($appointmentDetails->id);
        $this->assertModelData($fakeAppointmentDetails, $dbAppointmentDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_appointment_details()
    {
        $appointmentDetails = factory(AppointmentDetails::class)->create();

        $resp = $this->appointmentDetailsRepo->delete($appointmentDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(AppointmentDetails::find($appointmentDetails->id), 'AppointmentDetails should not exist in DB');
    }
}
