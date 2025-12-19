<?php namespace Tests\Repositories;

use App\Models\PaymentType;
use App\Repositories\PaymentTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PaymentTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaymentTypeRepository
     */
    protected $paymentTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->paymentTypeRepo = \App::make(PaymentTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_payment_type()
    {
        $paymentType = factory(PaymentType::class)->make()->toArray();

        $createdPaymentType = $this->paymentTypeRepo->create($paymentType);

        $createdPaymentType = $createdPaymentType->toArray();
        $this->assertArrayHasKey('id', $createdPaymentType);
        $this->assertNotNull($createdPaymentType['id'], 'Created PaymentType must have id specified');
        $this->assertNotNull(PaymentType::find($createdPaymentType['id']), 'PaymentType with given id must be in DB');
        $this->assertModelData($paymentType, $createdPaymentType);
    }

    /**
     * @test read
     */
    public function test_read_payment_type()
    {
        $paymentType = factory(PaymentType::class)->create();

        $dbPaymentType = $this->paymentTypeRepo->find($paymentType->id);

        $dbPaymentType = $dbPaymentType->toArray();
        $this->assertModelData($paymentType->toArray(), $dbPaymentType);
    }

    /**
     * @test update
     */
    public function test_update_payment_type()
    {
        $paymentType = factory(PaymentType::class)->create();
        $fakePaymentType = factory(PaymentType::class)->make()->toArray();

        $updatedPaymentType = $this->paymentTypeRepo->update($fakePaymentType, $paymentType->id);

        $this->assertModelData($fakePaymentType, $updatedPaymentType->toArray());
        $dbPaymentType = $this->paymentTypeRepo->find($paymentType->id);
        $this->assertModelData($fakePaymentType, $dbPaymentType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_payment_type()
    {
        $paymentType = factory(PaymentType::class)->create();

        $resp = $this->paymentTypeRepo->delete($paymentType->id);

        $this->assertTrue($resp);
        $this->assertNull(PaymentType::find($paymentType->id), 'PaymentType should not exist in DB');
    }
}
