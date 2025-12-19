<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\RecurringVoucherSetupDetail;

class RecurringVoucherSetupDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_recurring_voucher_setup_detail()
    {
        $recurringVoucherSetupDetail = factory(RecurringVoucherSetupDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/recurring_voucher_setup_details', $recurringVoucherSetupDetail
        );

        $this->assertApiResponse($recurringVoucherSetupDetail);
    }

    /**
     * @test
     */
    public function test_read_recurring_voucher_setup_detail()
    {
        $recurringVoucherSetupDetail = factory(RecurringVoucherSetupDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/recurring_voucher_setup_details/'.$recurringVoucherSetupDetail->id
        );

        $this->assertApiResponse($recurringVoucherSetupDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_recurring_voucher_setup_detail()
    {
        $recurringVoucherSetupDetail = factory(RecurringVoucherSetupDetail::class)->create();
        $editedRecurringVoucherSetupDetail = factory(RecurringVoucherSetupDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/recurring_voucher_setup_details/'.$recurringVoucherSetupDetail->id,
            $editedRecurringVoucherSetupDetail
        );

        $this->assertApiResponse($editedRecurringVoucherSetupDetail);
    }

    /**
     * @test
     */
    public function test_delete_recurring_voucher_setup_detail()
    {
        $recurringVoucherSetupDetail = factory(RecurringVoucherSetupDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/recurring_voucher_setup_details/'.$recurringVoucherSetupDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/recurring_voucher_setup_details/'.$recurringVoucherSetupDetail->id
        );

        $this->response->assertStatus(404);
    }
}
