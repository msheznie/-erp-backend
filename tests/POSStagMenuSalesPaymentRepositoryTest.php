<?php namespace Tests\Repositories;

use App\Models\POSStagMenuSalesPayment;
use App\Repositories\POSStagMenuSalesPaymentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSStagMenuSalesPaymentRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSStagMenuSalesPaymentRepository
     */
    protected $pOSStagMenuSalesPaymentRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSStagMenuSalesPaymentRepo = \App::make(POSStagMenuSalesPaymentRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_stag_menu_sales_payment()
    {
        $pOSStagMenuSalesPayment = factory(POSStagMenuSalesPayment::class)->make()->toArray();

        $createdPOSStagMenuSalesPayment = $this->pOSStagMenuSalesPaymentRepo->create($pOSStagMenuSalesPayment);

        $createdPOSStagMenuSalesPayment = $createdPOSStagMenuSalesPayment->toArray();
        $this->assertArrayHasKey('id', $createdPOSStagMenuSalesPayment);
        $this->assertNotNull($createdPOSStagMenuSalesPayment['id'], 'Created POSStagMenuSalesPayment must have id specified');
        $this->assertNotNull(POSStagMenuSalesPayment::find($createdPOSStagMenuSalesPayment['id']), 'POSStagMenuSalesPayment with given id must be in DB');
        $this->assertModelData($pOSStagMenuSalesPayment, $createdPOSStagMenuSalesPayment);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_stag_menu_sales_payment()
    {
        $pOSStagMenuSalesPayment = factory(POSStagMenuSalesPayment::class)->create();

        $dbPOSStagMenuSalesPayment = $this->pOSStagMenuSalesPaymentRepo->find($pOSStagMenuSalesPayment->id);

        $dbPOSStagMenuSalesPayment = $dbPOSStagMenuSalesPayment->toArray();
        $this->assertModelData($pOSStagMenuSalesPayment->toArray(), $dbPOSStagMenuSalesPayment);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_stag_menu_sales_payment()
    {
        $pOSStagMenuSalesPayment = factory(POSStagMenuSalesPayment::class)->create();
        $fakePOSStagMenuSalesPayment = factory(POSStagMenuSalesPayment::class)->make()->toArray();

        $updatedPOSStagMenuSalesPayment = $this->pOSStagMenuSalesPaymentRepo->update($fakePOSStagMenuSalesPayment, $pOSStagMenuSalesPayment->id);

        $this->assertModelData($fakePOSStagMenuSalesPayment, $updatedPOSStagMenuSalesPayment->toArray());
        $dbPOSStagMenuSalesPayment = $this->pOSStagMenuSalesPaymentRepo->find($pOSStagMenuSalesPayment->id);
        $this->assertModelData($fakePOSStagMenuSalesPayment, $dbPOSStagMenuSalesPayment->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_stag_menu_sales_payment()
    {
        $pOSStagMenuSalesPayment = factory(POSStagMenuSalesPayment::class)->create();

        $resp = $this->pOSStagMenuSalesPaymentRepo->delete($pOSStagMenuSalesPayment->id);

        $this->assertTrue($resp);
        $this->assertNull(POSStagMenuSalesPayment::find($pOSStagMenuSalesPayment->id), 'POSStagMenuSalesPayment should not exist in DB');
    }
}
