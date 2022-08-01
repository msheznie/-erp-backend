<?php namespace Tests\Repositories;

use App\Models\POSStagMenuSalesServiceCharge;
use App\Repositories\POSStagMenuSalesServiceChargeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSStagMenuSalesServiceChargeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSStagMenuSalesServiceChargeRepository
     */
    protected $pOSStagMenuSalesServiceChargeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSStagMenuSalesServiceChargeRepo = \App::make(POSStagMenuSalesServiceChargeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_stag_menu_sales_service_charge()
    {
        $pOSStagMenuSalesServiceCharge = factory(POSStagMenuSalesServiceCharge::class)->make()->toArray();

        $createdPOSStagMenuSalesServiceCharge = $this->pOSStagMenuSalesServiceChargeRepo->create($pOSStagMenuSalesServiceCharge);

        $createdPOSStagMenuSalesServiceCharge = $createdPOSStagMenuSalesServiceCharge->toArray();
        $this->assertArrayHasKey('id', $createdPOSStagMenuSalesServiceCharge);
        $this->assertNotNull($createdPOSStagMenuSalesServiceCharge['id'], 'Created POSStagMenuSalesServiceCharge must have id specified');
        $this->assertNotNull(POSStagMenuSalesServiceCharge::find($createdPOSStagMenuSalesServiceCharge['id']), 'POSStagMenuSalesServiceCharge with given id must be in DB');
        $this->assertModelData($pOSStagMenuSalesServiceCharge, $createdPOSStagMenuSalesServiceCharge);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_stag_menu_sales_service_charge()
    {
        $pOSStagMenuSalesServiceCharge = factory(POSStagMenuSalesServiceCharge::class)->create();

        $dbPOSStagMenuSalesServiceCharge = $this->pOSStagMenuSalesServiceChargeRepo->find($pOSStagMenuSalesServiceCharge->id);

        $dbPOSStagMenuSalesServiceCharge = $dbPOSStagMenuSalesServiceCharge->toArray();
        $this->assertModelData($pOSStagMenuSalesServiceCharge->toArray(), $dbPOSStagMenuSalesServiceCharge);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_stag_menu_sales_service_charge()
    {
        $pOSStagMenuSalesServiceCharge = factory(POSStagMenuSalesServiceCharge::class)->create();
        $fakePOSStagMenuSalesServiceCharge = factory(POSStagMenuSalesServiceCharge::class)->make()->toArray();

        $updatedPOSStagMenuSalesServiceCharge = $this->pOSStagMenuSalesServiceChargeRepo->update($fakePOSStagMenuSalesServiceCharge, $pOSStagMenuSalesServiceCharge->id);

        $this->assertModelData($fakePOSStagMenuSalesServiceCharge, $updatedPOSStagMenuSalesServiceCharge->toArray());
        $dbPOSStagMenuSalesServiceCharge = $this->pOSStagMenuSalesServiceChargeRepo->find($pOSStagMenuSalesServiceCharge->id);
        $this->assertModelData($fakePOSStagMenuSalesServiceCharge, $dbPOSStagMenuSalesServiceCharge->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_stag_menu_sales_service_charge()
    {
        $pOSStagMenuSalesServiceCharge = factory(POSStagMenuSalesServiceCharge::class)->create();

        $resp = $this->pOSStagMenuSalesServiceChargeRepo->delete($pOSStagMenuSalesServiceCharge->id);

        $this->assertTrue($resp);
        $this->assertNull(POSStagMenuSalesServiceCharge::find($pOSStagMenuSalesServiceCharge->id), 'POSStagMenuSalesServiceCharge should not exist in DB');
    }
}
