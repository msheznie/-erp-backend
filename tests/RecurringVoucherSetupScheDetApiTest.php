<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\RecurringVoucherSetupScheDet;

class RecurringVoucherSetupScheDetApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_recurring_voucher_setup_sche_det()
    {
        $recurringVoucherSetupScheDet = factory(RecurringVoucherSetupScheDet::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/recurring_voucher_setup_sche_dets', $recurringVoucherSetupScheDet
        );

        $this->assertApiResponse($recurringVoucherSetupScheDet);
    }

    /**
     * @test
     */
    public function test_read_recurring_voucher_setup_sche_det()
    {
        $recurringVoucherSetupScheDet = factory(RecurringVoucherSetupScheDet::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/recurring_voucher_setup_sche_dets/'.$recurringVoucherSetupScheDet->id
        );

        $this->assertApiResponse($recurringVoucherSetupScheDet->toArray());
    }

    /**
     * @test
     */
    public function test_update_recurring_voucher_setup_sche_det()
    {
        $recurringVoucherSetupScheDet = factory(RecurringVoucherSetupScheDet::class)->create();
        $editedRecurringVoucherSetupScheDet = factory(RecurringVoucherSetupScheDet::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/recurring_voucher_setup_sche_dets/'.$recurringVoucherSetupScheDet->id,
            $editedRecurringVoucherSetupScheDet
        );

        $this->assertApiResponse($editedRecurringVoucherSetupScheDet);
    }

    /**
     * @test
     */
    public function test_delete_recurring_voucher_setup_sche_det()
    {
        $recurringVoucherSetupScheDet = factory(RecurringVoucherSetupScheDet::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/recurring_voucher_setup_sche_dets/'.$recurringVoucherSetupScheDet->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/recurring_voucher_setup_sche_dets/'.$recurringVoucherSetupScheDet->id
        );

        $this->response->assertStatus(404);
    }
}
