<?php namespace Tests\Repositories;

use App\Models\POSSourceMenuSalesPayment;
use App\Repositories\POSSourceMenuSalesPaymentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSourceMenuSalesPaymentRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSourceMenuSalesPaymentRepository
     */
    protected $pOSSourceMenuSalesPaymentRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSourceMenuSalesPaymentRepo = \App::make(POSSourceMenuSalesPaymentRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_source_menu_sales_payment()
    {
        $pOSSourceMenuSalesPayment = factory(POSSourceMenuSalesPayment::class)->make()->toArray();

        $createdPOSSourceMenuSalesPayment = $this->pOSSourceMenuSalesPaymentRepo->create($pOSSourceMenuSalesPayment);

        $createdPOSSourceMenuSalesPayment = $createdPOSSourceMenuSalesPayment->toArray();
        $this->assertArrayHasKey('id', $createdPOSSourceMenuSalesPayment);
        $this->assertNotNull($createdPOSSourceMenuSalesPayment['id'], 'Created POSSourceMenuSalesPayment must have id specified');
        $this->assertNotNull(POSSourceMenuSalesPayment::find($createdPOSSourceMenuSalesPayment['id']), 'POSSourceMenuSalesPayment with given id must be in DB');
        $this->assertModelData($pOSSourceMenuSalesPayment, $createdPOSSourceMenuSalesPayment);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_source_menu_sales_payment()
    {
        $pOSSourceMenuSalesPayment = factory(POSSourceMenuSalesPayment::class)->create();

        $dbPOSSourceMenuSalesPayment = $this->pOSSourceMenuSalesPaymentRepo->find($pOSSourceMenuSalesPayment->id);

        $dbPOSSourceMenuSalesPayment = $dbPOSSourceMenuSalesPayment->toArray();
        $this->assertModelData($pOSSourceMenuSalesPayment->toArray(), $dbPOSSourceMenuSalesPayment);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_source_menu_sales_payment()
    {
        $pOSSourceMenuSalesPayment = factory(POSSourceMenuSalesPayment::class)->create();
        $fakePOSSourceMenuSalesPayment = factory(POSSourceMenuSalesPayment::class)->make()->toArray();

        $updatedPOSSourceMenuSalesPayment = $this->pOSSourceMenuSalesPaymentRepo->update($fakePOSSourceMenuSalesPayment, $pOSSourceMenuSalesPayment->id);

        $this->assertModelData($fakePOSSourceMenuSalesPayment, $updatedPOSSourceMenuSalesPayment->toArray());
        $dbPOSSourceMenuSalesPayment = $this->pOSSourceMenuSalesPaymentRepo->find($pOSSourceMenuSalesPayment->id);
        $this->assertModelData($fakePOSSourceMenuSalesPayment, $dbPOSSourceMenuSalesPayment->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_source_menu_sales_payment()
    {
        $pOSSourceMenuSalesPayment = factory(POSSourceMenuSalesPayment::class)->create();

        $resp = $this->pOSSourceMenuSalesPaymentRepo->delete($pOSSourceMenuSalesPayment->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSourceMenuSalesPayment::find($pOSSourceMenuSalesPayment->id), 'POSSourceMenuSalesPayment should not exist in DB');
    }
}
