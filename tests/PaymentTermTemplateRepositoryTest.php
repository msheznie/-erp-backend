<?php namespace Tests\Repositories;

use App\Models\PaymentTermTemplate;
use App\Repositories\PaymentTermTemplateRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PaymentTermTemplateRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaymentTermTemplateRepository
     */
    protected $paymentTermTemplateRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->paymentTermTemplateRepo = \App::make(PaymentTermTemplateRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_payment_term_template()
    {
        $paymentTermTemplate = factory(PaymentTermTemplate::class)->make()->toArray();

        $createdPaymentTermTemplate = $this->paymentTermTemplateRepo->create($paymentTermTemplate);

        $createdPaymentTermTemplate = $createdPaymentTermTemplate->toArray();
        $this->assertArrayHasKey('id', $createdPaymentTermTemplate);
        $this->assertNotNull($createdPaymentTermTemplate['id'], 'Created PaymentTermTemplate must have id specified');
        $this->assertNotNull(PaymentTermTemplate::find($createdPaymentTermTemplate['id']), 'PaymentTermTemplate with given id must be in DB');
        $this->assertModelData($paymentTermTemplate, $createdPaymentTermTemplate);
    }

    /**
     * @test read
     */
    public function test_read_payment_term_template()
    {
        $paymentTermTemplate = factory(PaymentTermTemplate::class)->create();

        $dbPaymentTermTemplate = $this->paymentTermTemplateRepo->find($paymentTermTemplate->id);

        $dbPaymentTermTemplate = $dbPaymentTermTemplate->toArray();
        $this->assertModelData($paymentTermTemplate->toArray(), $dbPaymentTermTemplate);
    }

    /**
     * @test update
     */
    public function test_update_payment_term_template()
    {
        $paymentTermTemplate = factory(PaymentTermTemplate::class)->create();
        $fakePaymentTermTemplate = factory(PaymentTermTemplate::class)->make()->toArray();

        $updatedPaymentTermTemplate = $this->paymentTermTemplateRepo->update($fakePaymentTermTemplate, $paymentTermTemplate->id);

        $this->assertModelData($fakePaymentTermTemplate, $updatedPaymentTermTemplate->toArray());
        $dbPaymentTermTemplate = $this->paymentTermTemplateRepo->find($paymentTermTemplate->id);
        $this->assertModelData($fakePaymentTermTemplate, $dbPaymentTermTemplate->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_payment_term_template()
    {
        $paymentTermTemplate = factory(PaymentTermTemplate::class)->create();

        $resp = $this->paymentTermTemplateRepo->delete($paymentTermTemplate->id);

        $this->assertTrue($resp);
        $this->assertNull(PaymentTermTemplate::find($paymentTermTemplate->id), 'PaymentTermTemplate should not exist in DB');
    }
}
