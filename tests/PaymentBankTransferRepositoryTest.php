<?php

use App\Models\PaymentBankTransfer;
use App\Repositories\PaymentBankTransferRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaymentBankTransferRepositoryTest extends TestCase
{
    use MakePaymentBankTransferTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaymentBankTransferRepository
     */
    protected $paymentBankTransferRepo;

    public function setUp()
    {
        parent::setUp();
        $this->paymentBankTransferRepo = App::make(PaymentBankTransferRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePaymentBankTransfer()
    {
        $paymentBankTransfer = $this->fakePaymentBankTransferData();
        $createdPaymentBankTransfer = $this->paymentBankTransferRepo->create($paymentBankTransfer);
        $createdPaymentBankTransfer = $createdPaymentBankTransfer->toArray();
        $this->assertArrayHasKey('id', $createdPaymentBankTransfer);
        $this->assertNotNull($createdPaymentBankTransfer['id'], 'Created PaymentBankTransfer must have id specified');
        $this->assertNotNull(PaymentBankTransfer::find($createdPaymentBankTransfer['id']), 'PaymentBankTransfer with given id must be in DB');
        $this->assertModelData($paymentBankTransfer, $createdPaymentBankTransfer);
    }

    /**
     * @test read
     */
    public function testReadPaymentBankTransfer()
    {
        $paymentBankTransfer = $this->makePaymentBankTransfer();
        $dbPaymentBankTransfer = $this->paymentBankTransferRepo->find($paymentBankTransfer->id);
        $dbPaymentBankTransfer = $dbPaymentBankTransfer->toArray();
        $this->assertModelData($paymentBankTransfer->toArray(), $dbPaymentBankTransfer);
    }

    /**
     * @test update
     */
    public function testUpdatePaymentBankTransfer()
    {
        $paymentBankTransfer = $this->makePaymentBankTransfer();
        $fakePaymentBankTransfer = $this->fakePaymentBankTransferData();
        $updatedPaymentBankTransfer = $this->paymentBankTransferRepo->update($fakePaymentBankTransfer, $paymentBankTransfer->id);
        $this->assertModelData($fakePaymentBankTransfer, $updatedPaymentBankTransfer->toArray());
        $dbPaymentBankTransfer = $this->paymentBankTransferRepo->find($paymentBankTransfer->id);
        $this->assertModelData($fakePaymentBankTransfer, $dbPaymentBankTransfer->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePaymentBankTransfer()
    {
        $paymentBankTransfer = $this->makePaymentBankTransfer();
        $resp = $this->paymentBankTransferRepo->delete($paymentBankTransfer->id);
        $this->assertTrue($resp);
        $this->assertNull(PaymentBankTransfer::find($paymentBankTransfer->id), 'PaymentBankTransfer should not exist in DB');
    }
}
