<?php namespace Tests\Repositories;

use App\Models\POSSOURCEMenuSalesOutletTaxes;
use App\Repositories\POSSOURCEMenuSalesOutletTaxesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSOURCEMenuSalesOutletTaxesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSOURCEMenuSalesOutletTaxesRepository
     */
    protected $pOSSOURCEMenuSalesOutletTaxesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSOURCEMenuSalesOutletTaxesRepo = \App::make(POSSOURCEMenuSalesOutletTaxesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes()
    {
        $pOSSOURCEMenuSalesOutletTaxes = factory(POSSOURCEMenuSalesOutletTaxes::class)->make()->toArray();

        $createdPOSSOURCEMenuSalesOutletTaxes = $this->pOSSOURCEMenuSalesOutletTaxesRepo->create($pOSSOURCEMenuSalesOutletTaxes);

        $createdPOSSOURCEMenuSalesOutletTaxes = $createdPOSSOURCEMenuSalesOutletTaxes->toArray();
        $this->assertArrayHasKey('id', $createdPOSSOURCEMenuSalesOutletTaxes);
        $this->assertNotNull($createdPOSSOURCEMenuSalesOutletTaxes['id'], 'Created POSSOURCEMenuSalesOutletTaxes must have id specified');
        $this->assertNotNull(POSSOURCEMenuSalesOutletTaxes::find($createdPOSSOURCEMenuSalesOutletTaxes['id']), 'POSSOURCEMenuSalesOutletTaxes with given id must be in DB');
        $this->assertModelData($pOSSOURCEMenuSalesOutletTaxes, $createdPOSSOURCEMenuSalesOutletTaxes);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes()
    {
        $pOSSOURCEMenuSalesOutletTaxes = factory(POSSOURCEMenuSalesOutletTaxes::class)->create();

        $dbPOSSOURCEMenuSalesOutletTaxes = $this->pOSSOURCEMenuSalesOutletTaxesRepo->find($pOSSOURCEMenuSalesOutletTaxes->id);

        $dbPOSSOURCEMenuSalesOutletTaxes = $dbPOSSOURCEMenuSalesOutletTaxes->toArray();
        $this->assertModelData($pOSSOURCEMenuSalesOutletTaxes->toArray(), $dbPOSSOURCEMenuSalesOutletTaxes);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes()
    {
        $pOSSOURCEMenuSalesOutletTaxes = factory(POSSOURCEMenuSalesOutletTaxes::class)->create();
        $fakePOSSOURCEMenuSalesOutletTaxes = factory(POSSOURCEMenuSalesOutletTaxes::class)->make()->toArray();

        $updatedPOSSOURCEMenuSalesOutletTaxes = $this->pOSSOURCEMenuSalesOutletTaxesRepo->update($fakePOSSOURCEMenuSalesOutletTaxes, $pOSSOURCEMenuSalesOutletTaxes->id);

        $this->assertModelData($fakePOSSOURCEMenuSalesOutletTaxes, $updatedPOSSOURCEMenuSalesOutletTaxes->toArray());
        $dbPOSSOURCEMenuSalesOutletTaxes = $this->pOSSOURCEMenuSalesOutletTaxesRepo->find($pOSSOURCEMenuSalesOutletTaxes->id);
        $this->assertModelData($fakePOSSOURCEMenuSalesOutletTaxes, $dbPOSSOURCEMenuSalesOutletTaxes->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes()
    {
        $pOSSOURCEMenuSalesOutletTaxes = factory(POSSOURCEMenuSalesOutletTaxes::class)->create();

        $resp = $this->pOSSOURCEMenuSalesOutletTaxesRepo->delete($pOSSOURCEMenuSalesOutletTaxes->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSOURCEMenuSalesOutletTaxes::find($pOSSOURCEMenuSalesOutletTaxes->id), 'POSSOURCEMenuSalesOutletTaxes should not exist in DB');
    }
}
