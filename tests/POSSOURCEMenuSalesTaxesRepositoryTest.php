<?php namespace Tests\Repositories;

use App\Models\POSSOURCEMenuSalesTaxes;
use App\Repositories\POSSOURCEMenuSalesTaxesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSOURCEMenuSalesTaxesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSOURCEMenuSalesTaxesRepository
     */
    protected $pOSSOURCEMenuSalesTaxesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSOURCEMenuSalesTaxesRepo = \App::make(POSSOURCEMenuSalesTaxesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_s_o_u_r_c_e_menu_sales_taxes()
    {
        $pOSSOURCEMenuSalesTaxes = factory(POSSOURCEMenuSalesTaxes::class)->make()->toArray();

        $createdPOSSOURCEMenuSalesTaxes = $this->pOSSOURCEMenuSalesTaxesRepo->create($pOSSOURCEMenuSalesTaxes);

        $createdPOSSOURCEMenuSalesTaxes = $createdPOSSOURCEMenuSalesTaxes->toArray();
        $this->assertArrayHasKey('id', $createdPOSSOURCEMenuSalesTaxes);
        $this->assertNotNull($createdPOSSOURCEMenuSalesTaxes['id'], 'Created POSSOURCEMenuSalesTaxes must have id specified');
        $this->assertNotNull(POSSOURCEMenuSalesTaxes::find($createdPOSSOURCEMenuSalesTaxes['id']), 'POSSOURCEMenuSalesTaxes with given id must be in DB');
        $this->assertModelData($pOSSOURCEMenuSalesTaxes, $createdPOSSOURCEMenuSalesTaxes);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_s_o_u_r_c_e_menu_sales_taxes()
    {
        $pOSSOURCEMenuSalesTaxes = factory(POSSOURCEMenuSalesTaxes::class)->create();

        $dbPOSSOURCEMenuSalesTaxes = $this->pOSSOURCEMenuSalesTaxesRepo->find($pOSSOURCEMenuSalesTaxes->id);

        $dbPOSSOURCEMenuSalesTaxes = $dbPOSSOURCEMenuSalesTaxes->toArray();
        $this->assertModelData($pOSSOURCEMenuSalesTaxes->toArray(), $dbPOSSOURCEMenuSalesTaxes);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_s_o_u_r_c_e_menu_sales_taxes()
    {
        $pOSSOURCEMenuSalesTaxes = factory(POSSOURCEMenuSalesTaxes::class)->create();
        $fakePOSSOURCEMenuSalesTaxes = factory(POSSOURCEMenuSalesTaxes::class)->make()->toArray();

        $updatedPOSSOURCEMenuSalesTaxes = $this->pOSSOURCEMenuSalesTaxesRepo->update($fakePOSSOURCEMenuSalesTaxes, $pOSSOURCEMenuSalesTaxes->id);

        $this->assertModelData($fakePOSSOURCEMenuSalesTaxes, $updatedPOSSOURCEMenuSalesTaxes->toArray());
        $dbPOSSOURCEMenuSalesTaxes = $this->pOSSOURCEMenuSalesTaxesRepo->find($pOSSOURCEMenuSalesTaxes->id);
        $this->assertModelData($fakePOSSOURCEMenuSalesTaxes, $dbPOSSOURCEMenuSalesTaxes->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_s_o_u_r_c_e_menu_sales_taxes()
    {
        $pOSSOURCEMenuSalesTaxes = factory(POSSOURCEMenuSalesTaxes::class)->create();

        $resp = $this->pOSSOURCEMenuSalesTaxesRepo->delete($pOSSOURCEMenuSalesTaxes->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSOURCEMenuSalesTaxes::find($pOSSOURCEMenuSalesTaxes->id), 'POSSOURCEMenuSalesTaxes should not exist in DB');
    }
}
