<?php namespace Tests\Repositories;

use App\Models\PaymentTermTemplateAssigned;
use App\Repositories\PaymentTermTemplateAssignedRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PaymentTermTemplateAssignedRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaymentTermTemplateAssignedRepository
     */
    protected $paymentTermTemplateAssignedRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->paymentTermTemplateAssignedRepo = \App::make(PaymentTermTemplateAssignedRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_payment_term_template_assigned()
    {
        $paymentTermTemplateAssigned = factory(PaymentTermTemplateAssigned::class)->make()->toArray();

        $createdPaymentTermTemplateAssigned = $this->paymentTermTemplateAssignedRepo->create($paymentTermTemplateAssigned);

        $createdPaymentTermTemplateAssigned = $createdPaymentTermTemplateAssigned->toArray();
        $this->assertArrayHasKey('id', $createdPaymentTermTemplateAssigned);
        $this->assertNotNull($createdPaymentTermTemplateAssigned['id'], 'Created PaymentTermTemplateAssigned must have id specified');
        $this->assertNotNull(PaymentTermTemplateAssigned::find($createdPaymentTermTemplateAssigned['id']), 'PaymentTermTemplateAssigned with given id must be in DB');
        $this->assertModelData($paymentTermTemplateAssigned, $createdPaymentTermTemplateAssigned);
    }

    /**
     * @test read
     */
    public function test_read_payment_term_template_assigned()
    {
        $paymentTermTemplateAssigned = factory(PaymentTermTemplateAssigned::class)->create();

        $dbPaymentTermTemplateAssigned = $this->paymentTermTemplateAssignedRepo->find($paymentTermTemplateAssigned->id);

        $dbPaymentTermTemplateAssigned = $dbPaymentTermTemplateAssigned->toArray();
        $this->assertModelData($paymentTermTemplateAssigned->toArray(), $dbPaymentTermTemplateAssigned);
    }

    /**
     * @test update
     */
    public function test_update_payment_term_template_assigned()
    {
        $paymentTermTemplateAssigned = factory(PaymentTermTemplateAssigned::class)->create();
        $fakePaymentTermTemplateAssigned = factory(PaymentTermTemplateAssigned::class)->make()->toArray();

        $updatedPaymentTermTemplateAssigned = $this->paymentTermTemplateAssignedRepo->update($fakePaymentTermTemplateAssigned, $paymentTermTemplateAssigned->id);

        $this->assertModelData($fakePaymentTermTemplateAssigned, $updatedPaymentTermTemplateAssigned->toArray());
        $dbPaymentTermTemplateAssigned = $this->paymentTermTemplateAssignedRepo->find($paymentTermTemplateAssigned->id);
        $this->assertModelData($fakePaymentTermTemplateAssigned, $dbPaymentTermTemplateAssigned->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_payment_term_template_assigned()
    {
        $paymentTermTemplateAssigned = factory(PaymentTermTemplateAssigned::class)->create();

        $resp = $this->paymentTermTemplateAssignedRepo->delete($paymentTermTemplateAssigned->id);

        $this->assertTrue($resp);
        $this->assertNull(PaymentTermTemplateAssigned::find($paymentTermTemplateAssigned->id), 'PaymentTermTemplateAssigned should not exist in DB');
    }
}
