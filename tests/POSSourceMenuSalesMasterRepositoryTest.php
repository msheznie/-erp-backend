<?php namespace Tests\Repositories;

use App\Models\POSSourceMenuSalesMaster;
use App\Repositories\POSSourceMenuSalesMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSourceMenuSalesMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSourceMenuSalesMasterRepository
     */
    protected $pOSSourceMenuSalesMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSourceMenuSalesMasterRepo = \App::make(POSSourceMenuSalesMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_source_menu_sales_master()
    {
        $pOSSourceMenuSalesMaster = factory(POSSourceMenuSalesMaster::class)->make()->toArray();

        $createdPOSSourceMenuSalesMaster = $this->pOSSourceMenuSalesMasterRepo->create($pOSSourceMenuSalesMaster);

        $createdPOSSourceMenuSalesMaster = $createdPOSSourceMenuSalesMaster->toArray();
        $this->assertArrayHasKey('id', $createdPOSSourceMenuSalesMaster);
        $this->assertNotNull($createdPOSSourceMenuSalesMaster['id'], 'Created POSSourceMenuSalesMaster must have id specified');
        $this->assertNotNull(POSSourceMenuSalesMaster::find($createdPOSSourceMenuSalesMaster['id']), 'POSSourceMenuSalesMaster with given id must be in DB');
        $this->assertModelData($pOSSourceMenuSalesMaster, $createdPOSSourceMenuSalesMaster);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_source_menu_sales_master()
    {
        $pOSSourceMenuSalesMaster = factory(POSSourceMenuSalesMaster::class)->create();

        $dbPOSSourceMenuSalesMaster = $this->pOSSourceMenuSalesMasterRepo->find($pOSSourceMenuSalesMaster->id);

        $dbPOSSourceMenuSalesMaster = $dbPOSSourceMenuSalesMaster->toArray();
        $this->assertModelData($pOSSourceMenuSalesMaster->toArray(), $dbPOSSourceMenuSalesMaster);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_source_menu_sales_master()
    {
        $pOSSourceMenuSalesMaster = factory(POSSourceMenuSalesMaster::class)->create();
        $fakePOSSourceMenuSalesMaster = factory(POSSourceMenuSalesMaster::class)->make()->toArray();

        $updatedPOSSourceMenuSalesMaster = $this->pOSSourceMenuSalesMasterRepo->update($fakePOSSourceMenuSalesMaster, $pOSSourceMenuSalesMaster->id);

        $this->assertModelData($fakePOSSourceMenuSalesMaster, $updatedPOSSourceMenuSalesMaster->toArray());
        $dbPOSSourceMenuSalesMaster = $this->pOSSourceMenuSalesMasterRepo->find($pOSSourceMenuSalesMaster->id);
        $this->assertModelData($fakePOSSourceMenuSalesMaster, $dbPOSSourceMenuSalesMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_source_menu_sales_master()
    {
        $pOSSourceMenuSalesMaster = factory(POSSourceMenuSalesMaster::class)->create();

        $resp = $this->pOSSourceMenuSalesMasterRepo->delete($pOSSourceMenuSalesMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSourceMenuSalesMaster::find($pOSSourceMenuSalesMaster->id), 'POSSourceMenuSalesMaster should not exist in DB');
    }
}
