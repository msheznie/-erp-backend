<?php namespace Tests\Repositories;

use App\Models\PaymentTermConfig;
use App\Repositories\PaymentTermConfigRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PaymentTermConfigRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaymentTermConfigRepository
     */
    protected $paymentTermConfigRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->paymentTermConfigRepo = \App::make(PaymentTermConfigRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_payment_term_config()
    {
        $paymentTermConfig = factory(PaymentTermConfig::class)->make()->toArray();

        $createdPaymentTermConfig = $this->paymentTermConfigRepo->create($paymentTermConfig);

        $createdPaymentTermConfig = $createdPaymentTermConfig->toArray();
        $this->assertArrayHasKey('id', $createdPaymentTermConfig);
        $this->assertNotNull($createdPaymentTermConfig['id'], 'Created PaymentTermConfig must have id specified');
        $this->assertNotNull(PaymentTermConfig::find($createdPaymentTermConfig['id']), 'PaymentTermConfig with given id must be in DB');
        $this->assertModelData($paymentTermConfig, $createdPaymentTermConfig);
    }

    /**
     * @test read
     */
    public function test_read_payment_term_config()
    {
        $paymentTermConfig = factory(PaymentTermConfig::class)->create();

        $dbPaymentTermConfig = $this->paymentTermConfigRepo->find($paymentTermConfig->id);

        $dbPaymentTermConfig = $dbPaymentTermConfig->toArray();
        $this->assertModelData($paymentTermConfig->toArray(), $dbPaymentTermConfig);
    }

    /**
     * @test update
     */
    public function test_update_payment_term_config()
    {
        $paymentTermConfig = factory(PaymentTermConfig::class)->create();
        $fakePaymentTermConfig = factory(PaymentTermConfig::class)->make()->toArray();

        $updatedPaymentTermConfig = $this->paymentTermConfigRepo->update($fakePaymentTermConfig, $paymentTermConfig->id);

        $this->assertModelData($fakePaymentTermConfig, $updatedPaymentTermConfig->toArray());
        $dbPaymentTermConfig = $this->paymentTermConfigRepo->find($paymentTermConfig->id);
        $this->assertModelData($fakePaymentTermConfig, $dbPaymentTermConfig->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_payment_term_config()
    {
        $paymentTermConfig = factory(PaymentTermConfig::class)->create();

        $resp = $this->paymentTermConfigRepo->delete($paymentTermConfig->id);

        $this->assertTrue($resp);
        $this->assertNull(PaymentTermConfig::find($paymentTermConfig->id), 'PaymentTermConfig should not exist in DB');
    }
}
