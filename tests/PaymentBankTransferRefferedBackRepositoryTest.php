<?php

use App\Models\PaymentBankTransferRefferedBack;
use App\Repositories\PaymentBankTransferRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaymentBankTransferRefferedBackRepositoryTest extends TestCase
{
    use MakePaymentBankTransferRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaymentBankTransferRefferedBackRepository
     */
    protected $paymentBankTransferRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->paymentBankTransferRefferedBackRepo = App::make(PaymentBankTransferRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePaymentBankTransferRefferedBack()
    {
        $paymentBankTransferRefferedBack = $this->fakePaymentBankTransferRefferedBackData();
        $createdPaymentBankTransferRefferedBack = $this->paymentBankTransferRefferedBackRepo->create($paymentBankTransferRefferedBack);
        $createdPaymentBankTransferRefferedBack = $createdPaymentBankTransferRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdPaymentBankTransferRefferedBack);
        $this->assertNotNull($createdPaymentBankTransferRefferedBack['id'], 'Created PaymentBankTransferRefferedBack must have id specified');
        $this->assertNotNull(PaymentBankTransferRefferedBack::find($createdPaymentBankTransferRefferedBack['id']), 'PaymentBankTransferRefferedBack with given id must be in DB');
        $this->assertModelData($paymentBankTransferRefferedBack, $createdPaymentBankTransferRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadPaymentBankTransferRefferedBack()
    {
        $paymentBankTransferRefferedBack = $this->makePaymentBankTransferRefferedBack();
        $dbPaymentBankTransferRefferedBack = $this->paymentBankTransferRefferedBackRepo->find($paymentBankTransferRefferedBack->id);
        $dbPaymentBankTransferRefferedBack = $dbPaymentBankTransferRefferedBack->toArray();
        $this->assertModelData($paymentBankTransferRefferedBack->toArray(), $dbPaymentBankTransferRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdatePaymentBankTransferRefferedBack()
    {
        $paymentBankTransferRefferedBack = $this->makePaymentBankTransferRefferedBack();
        $fakePaymentBankTransferRefferedBack = $this->fakePaymentBankTransferRefferedBackData();
        $updatedPaymentBankTransferRefferedBack = $this->paymentBankTransferRefferedBackRepo->update($fakePaymentBankTransferRefferedBack, $paymentBankTransferRefferedBack->id);
        $this->assertModelData($fakePaymentBankTransferRefferedBack, $updatedPaymentBankTransferRefferedBack->toArray());
        $dbPaymentBankTransferRefferedBack = $this->paymentBankTransferRefferedBackRepo->find($paymentBankTransferRefferedBack->id);
        $this->assertModelData($fakePaymentBankTransferRefferedBack, $dbPaymentBankTransferRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePaymentBankTransferRefferedBack()
    {
        $paymentBankTransferRefferedBack = $this->makePaymentBankTransferRefferedBack();
        $resp = $this->paymentBankTransferRefferedBackRepo->delete($paymentBankTransferRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(PaymentBankTransferRefferedBack::find($paymentBankTransferRefferedBack->id), 'PaymentBankTransferRefferedBack should not exist in DB');
    }
}
