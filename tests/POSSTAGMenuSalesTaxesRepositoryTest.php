<?php namespace Tests\Repositories;

use App\Models\POSSTAGMenuSalesTaxes;
use App\Repositories\POSSTAGMenuSalesTaxesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSTAGMenuSalesTaxesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSTAGMenuSalesTaxesRepository
     */
    protected $pOSSTAGMenuSalesTaxesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSTAGMenuSalesTaxesRepo = \App::make(POSSTAGMenuSalesTaxesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_s_t_a_g_menu_sales_taxes()
    {
        $pOSSTAGMenuSalesTaxes = factory(POSSTAGMenuSalesTaxes::class)->make()->toArray();

        $createdPOSSTAGMenuSalesTaxes = $this->pOSSTAGMenuSalesTaxesRepo->create($pOSSTAGMenuSalesTaxes);

        $createdPOSSTAGMenuSalesTaxes = $createdPOSSTAGMenuSalesTaxes->toArray();
        $this->assertArrayHasKey('id', $createdPOSSTAGMenuSalesTaxes);
        $this->assertNotNull($createdPOSSTAGMenuSalesTaxes['id'], 'Created POSSTAGMenuSalesTaxes must have id specified');
        $this->assertNotNull(POSSTAGMenuSalesTaxes::find($createdPOSSTAGMenuSalesTaxes['id']), 'POSSTAGMenuSalesTaxes with given id must be in DB');
        $this->assertModelData($pOSSTAGMenuSalesTaxes, $createdPOSSTAGMenuSalesTaxes);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_s_t_a_g_menu_sales_taxes()
    {
        $pOSSTAGMenuSalesTaxes = factory(POSSTAGMenuSalesTaxes::class)->create();

        $dbPOSSTAGMenuSalesTaxes = $this->pOSSTAGMenuSalesTaxesRepo->find($pOSSTAGMenuSalesTaxes->id);

        $dbPOSSTAGMenuSalesTaxes = $dbPOSSTAGMenuSalesTaxes->toArray();
        $this->assertModelData($pOSSTAGMenuSalesTaxes->toArray(), $dbPOSSTAGMenuSalesTaxes);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_s_t_a_g_menu_sales_taxes()
    {
        $pOSSTAGMenuSalesTaxes = factory(POSSTAGMenuSalesTaxes::class)->create();
        $fakePOSSTAGMenuSalesTaxes = factory(POSSTAGMenuSalesTaxes::class)->make()->toArray();

        $updatedPOSSTAGMenuSalesTaxes = $this->pOSSTAGMenuSalesTaxesRepo->update($fakePOSSTAGMenuSalesTaxes, $pOSSTAGMenuSalesTaxes->id);

        $this->assertModelData($fakePOSSTAGMenuSalesTaxes, $updatedPOSSTAGMenuSalesTaxes->toArray());
        $dbPOSSTAGMenuSalesTaxes = $this->pOSSTAGMenuSalesTaxesRepo->find($pOSSTAGMenuSalesTaxes->id);
        $this->assertModelData($fakePOSSTAGMenuSalesTaxes, $dbPOSSTAGMenuSalesTaxes->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_s_t_a_g_menu_sales_taxes()
    {
        $pOSSTAGMenuSalesTaxes = factory(POSSTAGMenuSalesTaxes::class)->create();

        $resp = $this->pOSSTAGMenuSalesTaxesRepo->delete($pOSSTAGMenuSalesTaxes->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSTAGMenuSalesTaxes::find($pOSSTAGMenuSalesTaxes->id), 'POSSTAGMenuSalesTaxes should not exist in DB');
    }
}
