<?php namespace Tests\Repositories;

use App\Models\SalesOrderAdvPayment;
use App\Repositories\SalesOrderAdvPaymentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SalesOrderAdvPaymentRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SalesOrderAdvPaymentRepository
     */
    protected $salesOrderAdvPaymentRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->salesOrderAdvPaymentRepo = \App::make(SalesOrderAdvPaymentRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_sales_order_adv_payment()
    {
        $salesOrderAdvPayment = factory(SalesOrderAdvPayment::class)->make()->toArray();

        $createdSalesOrderAdvPayment = $this->salesOrderAdvPaymentRepo->create($salesOrderAdvPayment);

        $createdSalesOrderAdvPayment = $createdSalesOrderAdvPayment->toArray();
        $this->assertArrayHasKey('id', $createdSalesOrderAdvPayment);
        $this->assertNotNull($createdSalesOrderAdvPayment['id'], 'Created SalesOrderAdvPayment must have id specified');
        $this->assertNotNull(SalesOrderAdvPayment::find($createdSalesOrderAdvPayment['id']), 'SalesOrderAdvPayment with given id must be in DB');
        $this->assertModelData($salesOrderAdvPayment, $createdSalesOrderAdvPayment);
    }

    /**
     * @test read
     */
    public function test_read_sales_order_adv_payment()
    {
        $salesOrderAdvPayment = factory(SalesOrderAdvPayment::class)->create();

        $dbSalesOrderAdvPayment = $this->salesOrderAdvPaymentRepo->find($salesOrderAdvPayment->id);

        $dbSalesOrderAdvPayment = $dbSalesOrderAdvPayment->toArray();
        $this->assertModelData($salesOrderAdvPayment->toArray(), $dbSalesOrderAdvPayment);
    }

    /**
     * @test update
     */
    public function test_update_sales_order_adv_payment()
    {
        $salesOrderAdvPayment = factory(SalesOrderAdvPayment::class)->create();
        $fakeSalesOrderAdvPayment = factory(SalesOrderAdvPayment::class)->make()->toArray();

        $updatedSalesOrderAdvPayment = $this->salesOrderAdvPaymentRepo->update($fakeSalesOrderAdvPayment, $salesOrderAdvPayment->id);

        $this->assertModelData($fakeSalesOrderAdvPayment, $updatedSalesOrderAdvPayment->toArray());
        $dbSalesOrderAdvPayment = $this->salesOrderAdvPaymentRepo->find($salesOrderAdvPayment->id);
        $this->assertModelData($fakeSalesOrderAdvPayment, $dbSalesOrderAdvPayment->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_sales_order_adv_payment()
    {
        $salesOrderAdvPayment = factory(SalesOrderAdvPayment::class)->create();

        $resp = $this->salesOrderAdvPaymentRepo->delete($salesOrderAdvPayment->id);

        $this->assertTrue($resp);
        $this->assertNull(SalesOrderAdvPayment::find($salesOrderAdvPayment->id), 'SalesOrderAdvPayment should not exist in DB');
    }
}
