<?php namespace Tests\Repositories;

use App\Models\PaymentVoucherBankChargeDetails;
use App\Repositories\PaymentVoucherBankChargeDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PaymentVoucherBankChargeDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaymentVoucherBankChargeDetailsRepository
     */
    protected $paymentVoucherBankChargeDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->paymentVoucherBankChargeDetailsRepo = \App::make(PaymentVoucherBankChargeDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_payment_voucher_bank_charge_details()
    {
        $paymentVoucherBankChargeDetails = factory(PaymentVoucherBankChargeDetails::class)->make()->toArray();

        $createdPaymentVoucherBankChargeDetails = $this->paymentVoucherBankChargeDetailsRepo->create($paymentVoucherBankChargeDetails);

        $createdPaymentVoucherBankChargeDetails = $createdPaymentVoucherBankChargeDetails->toArray();
        $this->assertArrayHasKey('id', $createdPaymentVoucherBankChargeDetails);
        $this->assertNotNull($createdPaymentVoucherBankChargeDetails['id'], 'Created PaymentVoucherBankChargeDetails must have id specified');
        $this->assertNotNull(PaymentVoucherBankChargeDetails::find($createdPaymentVoucherBankChargeDetails['id']), 'PaymentVoucherBankChargeDetails with given id must be in DB');
        $this->assertModelData($paymentVoucherBankChargeDetails, $createdPaymentVoucherBankChargeDetails);
    }

    /**
     * @test read
     */
    public function test_read_payment_voucher_bank_charge_details()
    {
        $paymentVoucherBankChargeDetails = factory(PaymentVoucherBankChargeDetails::class)->create();

        $dbPaymentVoucherBankChargeDetails = $this->paymentVoucherBankChargeDetailsRepo->find($paymentVoucherBankChargeDetails->id);

        $dbPaymentVoucherBankChargeDetails = $dbPaymentVoucherBankChargeDetails->toArray();
        $this->assertModelData($paymentVoucherBankChargeDetails->toArray(), $dbPaymentVoucherBankChargeDetails);
    }

    /**
     * @test update
     */
    public function test_update_payment_voucher_bank_charge_details()
    {
        $paymentVoucherBankChargeDetails = factory(PaymentVoucherBankChargeDetails::class)->create();
        $fakePaymentVoucherBankChargeDetails = factory(PaymentVoucherBankChargeDetails::class)->make()->toArray();

        $updatedPaymentVoucherBankChargeDetails = $this->paymentVoucherBankChargeDetailsRepo->update($fakePaymentVoucherBankChargeDetails, $paymentVoucherBankChargeDetails->id);

        $this->assertModelData($fakePaymentVoucherBankChargeDetails, $updatedPaymentVoucherBankChargeDetails->toArray());
        $dbPaymentVoucherBankChargeDetails = $this->paymentVoucherBankChargeDetailsRepo->find($paymentVoucherBankChargeDetails->id);
        $this->assertModelData($fakePaymentVoucherBankChargeDetails, $dbPaymentVoucherBankChargeDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_payment_voucher_bank_charge_details()
    {
        $paymentVoucherBankChargeDetails = factory(PaymentVoucherBankChargeDetails::class)->create();

        $resp = $this->paymentVoucherBankChargeDetailsRepo->delete($paymentVoucherBankChargeDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(PaymentVoucherBankChargeDetails::find($paymentVoucherBankChargeDetails->id), 'PaymentVoucherBankChargeDetails should not exist in DB');
    }
}
