<?php namespace Tests\Repositories;

use App\Models\POSSTAGMenuSalesOutletTaxes;
use App\Repositories\POSSTAGMenuSalesOutletTaxesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSTAGMenuSalesOutletTaxesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSTAGMenuSalesOutletTaxesRepository
     */
    protected $pOSSTAGMenuSalesOutletTaxesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSTAGMenuSalesOutletTaxesRepo = \App::make(POSSTAGMenuSalesOutletTaxesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_s_t_a_g_menu_sales_outlet_taxes()
    {
        $pOSSTAGMenuSalesOutletTaxes = factory(POSSTAGMenuSalesOutletTaxes::class)->make()->toArray();

        $createdPOSSTAGMenuSalesOutletTaxes = $this->pOSSTAGMenuSalesOutletTaxesRepo->create($pOSSTAGMenuSalesOutletTaxes);

        $createdPOSSTAGMenuSalesOutletTaxes = $createdPOSSTAGMenuSalesOutletTaxes->toArray();
        $this->assertArrayHasKey('id', $createdPOSSTAGMenuSalesOutletTaxes);
        $this->assertNotNull($createdPOSSTAGMenuSalesOutletTaxes['id'], 'Created POSSTAGMenuSalesOutletTaxes must have id specified');
        $this->assertNotNull(POSSTAGMenuSalesOutletTaxes::find($createdPOSSTAGMenuSalesOutletTaxes['id']), 'POSSTAGMenuSalesOutletTaxes with given id must be in DB');
        $this->assertModelData($pOSSTAGMenuSalesOutletTaxes, $createdPOSSTAGMenuSalesOutletTaxes);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_s_t_a_g_menu_sales_outlet_taxes()
    {
        $pOSSTAGMenuSalesOutletTaxes = factory(POSSTAGMenuSalesOutletTaxes::class)->create();

        $dbPOSSTAGMenuSalesOutletTaxes = $this->pOSSTAGMenuSalesOutletTaxesRepo->find($pOSSTAGMenuSalesOutletTaxes->id);

        $dbPOSSTAGMenuSalesOutletTaxes = $dbPOSSTAGMenuSalesOutletTaxes->toArray();
        $this->assertModelData($pOSSTAGMenuSalesOutletTaxes->toArray(), $dbPOSSTAGMenuSalesOutletTaxes);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_s_t_a_g_menu_sales_outlet_taxes()
    {
        $pOSSTAGMenuSalesOutletTaxes = factory(POSSTAGMenuSalesOutletTaxes::class)->create();
        $fakePOSSTAGMenuSalesOutletTaxes = factory(POSSTAGMenuSalesOutletTaxes::class)->make()->toArray();

        $updatedPOSSTAGMenuSalesOutletTaxes = $this->pOSSTAGMenuSalesOutletTaxesRepo->update($fakePOSSTAGMenuSalesOutletTaxes, $pOSSTAGMenuSalesOutletTaxes->id);

        $this->assertModelData($fakePOSSTAGMenuSalesOutletTaxes, $updatedPOSSTAGMenuSalesOutletTaxes->toArray());
        $dbPOSSTAGMenuSalesOutletTaxes = $this->pOSSTAGMenuSalesOutletTaxesRepo->find($pOSSTAGMenuSalesOutletTaxes->id);
        $this->assertModelData($fakePOSSTAGMenuSalesOutletTaxes, $dbPOSSTAGMenuSalesOutletTaxes->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_s_t_a_g_menu_sales_outlet_taxes()
    {
        $pOSSTAGMenuSalesOutletTaxes = factory(POSSTAGMenuSalesOutletTaxes::class)->create();

        $resp = $this->pOSSTAGMenuSalesOutletTaxesRepo->delete($pOSSTAGMenuSalesOutletTaxes->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSTAGMenuSalesOutletTaxes::find($pOSSTAGMenuSalesOutletTaxes->id), 'POSSTAGMenuSalesOutletTaxes should not exist in DB');
    }
}
