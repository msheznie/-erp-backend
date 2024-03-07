<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\RecurringVoucherSetupSchedule;

class RecurringVoucherSetupScheduleApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_recurring_voucher_setup_schedule()
    {
        $recurringVoucherSetupSchedule = factory(RecurringVoucherSetupSchedule::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/recurring_voucher_setup_schedules', $recurringVoucherSetupSchedule
        );

        $this->assertApiResponse($recurringVoucherSetupSchedule);
    }

    /**
     * @test
     */
    public function test_read_recurring_voucher_setup_schedule()
    {
        $recurringVoucherSetupSchedule = factory(RecurringVoucherSetupSchedule::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/recurring_voucher_setup_schedules/'.$recurringVoucherSetupSchedule->id
        );

        $this->assertApiResponse($recurringVoucherSetupSchedule->toArray());
    }

    /**
     * @test
     */
    public function test_update_recurring_voucher_setup_schedule()
    {
        $recurringVoucherSetupSchedule = factory(RecurringVoucherSetupSchedule::class)->create();
        $editedRecurringVoucherSetupSchedule = factory(RecurringVoucherSetupSchedule::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/recurring_voucher_setup_schedules/'.$recurringVoucherSetupSchedule->id,
            $editedRecurringVoucherSetupSchedule
        );

        $this->assertApiResponse($editedRecurringVoucherSetupSchedule);
    }

    /**
     * @test
     */
    public function test_delete_recurring_voucher_setup_schedule()
    {
        $recurringVoucherSetupSchedule = factory(RecurringVoucherSetupSchedule::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/recurring_voucher_setup_schedules/'.$recurringVoucherSetupSchedule->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/recurring_voucher_setup_schedules/'.$recurringVoucherSetupSchedule->id
        );

        $this->response->assertStatus(404);
    }
}
