<?php namespace Tests\Repositories;

use App\Models\Appointment;
use App\Repositories\AppointmentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AppointmentRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AppointmentRepository
     */
    protected $appointmentRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->appointmentRepo = \App::make(AppointmentRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_appointment()
    {
        $appointment = factory(Appointment::class)->make()->toArray();

        $createdAppointment = $this->appointmentRepo->create($appointment);

        $createdAppointment = $createdAppointment->toArray();
        $this->assertArrayHasKey('id', $createdAppointment);
        $this->assertNotNull($createdAppointment['id'], 'Created Appointment must have id specified');
        $this->assertNotNull(Appointment::find($createdAppointment['id']), 'Appointment with given id must be in DB');
        $this->assertModelData($appointment, $createdAppointment);
    }

    /**
     * @test read
     */
    public function test_read_appointment()
    {
        $appointment = factory(Appointment::class)->create();

        $dbAppointment = $this->appointmentRepo->find($appointment->id);

        $dbAppointment = $dbAppointment->toArray();
        $this->assertModelData($appointment->toArray(), $dbAppointment);
    }

    /**
     * @test update
     */
    public function test_update_appointment()
    {
        $appointment = factory(Appointment::class)->create();
        $fakeAppointment = factory(Appointment::class)->make()->toArray();

        $updatedAppointment = $this->appointmentRepo->update($fakeAppointment, $appointment->id);

        $this->assertModelData($fakeAppointment, $updatedAppointment->toArray());
        $dbAppointment = $this->appointmentRepo->find($appointment->id);
        $this->assertModelData($fakeAppointment, $dbAppointment->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_appointment()
    {
        $appointment = factory(Appointment::class)->create();

        $resp = $this->appointmentRepo->delete($appointment->id);

        $this->assertTrue($resp);
        $this->assertNull(Appointment::find($appointment->id), 'Appointment should not exist in DB');
    }
}
