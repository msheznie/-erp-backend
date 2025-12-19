<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\RecurringVoucherSetup;

class RecurringVoucherSetupApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_recurring_voucher_setup()
    {
        $recurringVoucherSetup = factory(RecurringVoucherSetup::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/recurring_voucher_setups', $recurringVoucherSetup
        );

        $this->assertApiResponse($recurringVoucherSetup);
    }

    /**
     * @test
     */
    public function test_read_recurring_voucher_setup()
    {
        $recurringVoucherSetup = factory(RecurringVoucherSetup::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/recurring_voucher_setups/'.$recurringVoucherSetup->id
        );

        $this->assertApiResponse($recurringVoucherSetup->toArray());
    }

    /**
     * @test
     */
    public function test_update_recurring_voucher_setup()
    {
        $recurringVoucherSetup = factory(RecurringVoucherSetup::class)->create();
        $editedRecurringVoucherSetup = factory(RecurringVoucherSetup::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/recurring_voucher_setups/'.$recurringVoucherSetup->id,
            $editedRecurringVoucherSetup
        );

        $this->assertApiResponse($editedRecurringVoucherSetup);
    }

    /**
     * @test
     */
    public function test_delete_recurring_voucher_setup()
    {
        $recurringVoucherSetup = factory(RecurringVoucherSetup::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/recurring_voucher_setups/'.$recurringVoucherSetup->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/recurring_voucher_setups/'.$recurringVoucherSetup->id
        );

        $this->response->assertStatus(404);
    }
}
