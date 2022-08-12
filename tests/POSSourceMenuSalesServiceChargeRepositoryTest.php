<?php namespace Tests\Repositories;

use App\Models\POSSourceMenuSalesServiceCharge;
use App\Repositories\POSSourceMenuSalesServiceChargeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSourceMenuSalesServiceChargeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSourceMenuSalesServiceChargeRepository
     */
    protected $pOSSourceMenuSalesServiceChargeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSourceMenuSalesServiceChargeRepo = \App::make(POSSourceMenuSalesServiceChargeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_source_menu_sales_service_charge()
    {
        $pOSSourceMenuSalesServiceCharge = factory(POSSourceMenuSalesServiceCharge::class)->make()->toArray();

        $createdPOSSourceMenuSalesServiceCharge = $this->pOSSourceMenuSalesServiceChargeRepo->create($pOSSourceMenuSalesServiceCharge);

        $createdPOSSourceMenuSalesServiceCharge = $createdPOSSourceMenuSalesServiceCharge->toArray();
        $this->assertArrayHasKey('id', $createdPOSSourceMenuSalesServiceCharge);
        $this->assertNotNull($createdPOSSourceMenuSalesServiceCharge['id'], 'Created POSSourceMenuSalesServiceCharge must have id specified');
        $this->assertNotNull(POSSourceMenuSalesServiceCharge::find($createdPOSSourceMenuSalesServiceCharge['id']), 'POSSourceMenuSalesServiceCharge with given id must be in DB');
        $this->assertModelData($pOSSourceMenuSalesServiceCharge, $createdPOSSourceMenuSalesServiceCharge);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_source_menu_sales_service_charge()
    {
        $pOSSourceMenuSalesServiceCharge = factory(POSSourceMenuSalesServiceCharge::class)->create();

        $dbPOSSourceMenuSalesServiceCharge = $this->pOSSourceMenuSalesServiceChargeRepo->find($pOSSourceMenuSalesServiceCharge->id);

        $dbPOSSourceMenuSalesServiceCharge = $dbPOSSourceMenuSalesServiceCharge->toArray();
        $this->assertModelData($pOSSourceMenuSalesServiceCharge->toArray(), $dbPOSSourceMenuSalesServiceCharge);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_source_menu_sales_service_charge()
    {
        $pOSSourceMenuSalesServiceCharge = factory(POSSourceMenuSalesServiceCharge::class)->create();
        $fakePOSSourceMenuSalesServiceCharge = factory(POSSourceMenuSalesServiceCharge::class)->make()->toArray();

        $updatedPOSSourceMenuSalesServiceCharge = $this->pOSSourceMenuSalesServiceChargeRepo->update($fakePOSSourceMenuSalesServiceCharge, $pOSSourceMenuSalesServiceCharge->id);

        $this->assertModelData($fakePOSSourceMenuSalesServiceCharge, $updatedPOSSourceMenuSalesServiceCharge->toArray());
        $dbPOSSourceMenuSalesServiceCharge = $this->pOSSourceMenuSalesServiceChargeRepo->find($pOSSourceMenuSalesServiceCharge->id);
        $this->assertModelData($fakePOSSourceMenuSalesServiceCharge, $dbPOSSourceMenuSalesServiceCharge->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_source_menu_sales_service_charge()
    {
        $pOSSourceMenuSalesServiceCharge = factory(POSSourceMenuSalesServiceCharge::class)->create();

        $resp = $this->pOSSourceMenuSalesServiceChargeRepo->delete($pOSSourceMenuSalesServiceCharge->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSourceMenuSalesServiceCharge::find($pOSSourceMenuSalesServiceCharge->id), 'POSSourceMenuSalesServiceCharge should not exist in DB');
    }
}
