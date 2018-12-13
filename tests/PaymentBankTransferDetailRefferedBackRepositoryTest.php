<?php

use App\Models\PaymentBankTransferDetailRefferedBack;
use App\Repositories\PaymentBankTransferDetailRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaymentBankTransferDetailRefferedBackRepositoryTest extends TestCase
{
    use MakePaymentBankTransferDetailRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaymentBankTransferDetailRefferedBackRepository
     */
    protected $paymentBankTransferDetailRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->paymentBankTransferDetailRefferedBackRepo = App::make(PaymentBankTransferDetailRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePaymentBankTransferDetailRefferedBack()
    {
        $paymentBankTransferDetailRefferedBack = $this->fakePaymentBankTransferDetailRefferedBackData();
        $createdPaymentBankTransferDetailRefferedBack = $this->paymentBankTransferDetailRefferedBackRepo->create($paymentBankTransferDetailRefferedBack);
        $createdPaymentBankTransferDetailRefferedBack = $createdPaymentBankTransferDetailRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdPaymentBankTransferDetailRefferedBack);
        $this->assertNotNull($createdPaymentBankTransferDetailRefferedBack['id'], 'Created PaymentBankTransferDetailRefferedBack must have id specified');
        $this->assertNotNull(PaymentBankTransferDetailRefferedBack::find($createdPaymentBankTransferDetailRefferedBack['id']), 'PaymentBankTransferDetailRefferedBack with given id must be in DB');
        $this->assertModelData($paymentBankTransferDetailRefferedBack, $createdPaymentBankTransferDetailRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadPaymentBankTransferDetailRefferedBack()
    {
        $paymentBankTransferDetailRefferedBack = $this->makePaymentBankTransferDetailRefferedBack();
        $dbPaymentBankTransferDetailRefferedBack = $this->paymentBankTransferDetailRefferedBackRepo->find($paymentBankTransferDetailRefferedBack->id);
        $dbPaymentBankTransferDetailRefferedBack = $dbPaymentBankTransferDetailRefferedBack->toArray();
        $this->assertModelData($paymentBankTransferDetailRefferedBack->toArray(), $dbPaymentBankTransferDetailRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdatePaymentBankTransferDetailRefferedBack()
    {
        $paymentBankTransferDetailRefferedBack = $this->makePaymentBankTransferDetailRefferedBack();
        $fakePaymentBankTransferDetailRefferedBack = $this->fakePaymentBankTransferDetailRefferedBackData();
        $updatedPaymentBankTransferDetailRefferedBack = $this->paymentBankTransferDetailRefferedBackRepo->update($fakePaymentBankTransferDetailRefferedBack, $paymentBankTransferDetailRefferedBack->id);
        $this->assertModelData($fakePaymentBankTransferDetailRefferedBack, $updatedPaymentBankTransferDetailRefferedBack->toArray());
        $dbPaymentBankTransferDetailRefferedBack = $this->paymentBankTransferDetailRefferedBackRepo->find($paymentBankTransferDetailRefferedBack->id);
        $this->assertModelData($fakePaymentBankTransferDetailRefferedBack, $dbPaymentBankTransferDetailRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePaymentBankTransferDetailRefferedBack()
    {
        $paymentBankTransferDetailRefferedBack = $this->makePaymentBankTransferDetailRefferedBack();
        $resp = $this->paymentBankTransferDetailRefferedBackRepo->delete($paymentBankTransferDetailRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(PaymentBankTransferDetailRefferedBack::find($paymentBankTransferDetailRefferedBack->id), 'PaymentBankTransferDetailRefferedBack should not exist in DB');
    }
}
