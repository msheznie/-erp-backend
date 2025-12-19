<?php namespace Tests\Repositories;

use App\Models\POSStagMenuSalesMaster;
use App\Repositories\POSStagMenuSalesMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSStagMenuSalesMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSStagMenuSalesMasterRepository
     */
    protected $pOSStagMenuSalesMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSStagMenuSalesMasterRepo = \App::make(POSStagMenuSalesMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_stag_menu_sales_master()
    {
        $pOSStagMenuSalesMaster = factory(POSStagMenuSalesMaster::class)->make()->toArray();

        $createdPOSStagMenuSalesMaster = $this->pOSStagMenuSalesMasterRepo->create($pOSStagMenuSalesMaster);

        $createdPOSStagMenuSalesMaster = $createdPOSStagMenuSalesMaster->toArray();
        $this->assertArrayHasKey('id', $createdPOSStagMenuSalesMaster);
        $this->assertNotNull($createdPOSStagMenuSalesMaster['id'], 'Created POSStagMenuSalesMaster must have id specified');
        $this->assertNotNull(POSStagMenuSalesMaster::find($createdPOSStagMenuSalesMaster['id']), 'POSStagMenuSalesMaster with given id must be in DB');
        $this->assertModelData($pOSStagMenuSalesMaster, $createdPOSStagMenuSalesMaster);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_stag_menu_sales_master()
    {
        $pOSStagMenuSalesMaster = factory(POSStagMenuSalesMaster::class)->create();

        $dbPOSStagMenuSalesMaster = $this->pOSStagMenuSalesMasterRepo->find($pOSStagMenuSalesMaster->id);

        $dbPOSStagMenuSalesMaster = $dbPOSStagMenuSalesMaster->toArray();
        $this->assertModelData($pOSStagMenuSalesMaster->toArray(), $dbPOSStagMenuSalesMaster);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_stag_menu_sales_master()
    {
        $pOSStagMenuSalesMaster = factory(POSStagMenuSalesMaster::class)->create();
        $fakePOSStagMenuSalesMaster = factory(POSStagMenuSalesMaster::class)->make()->toArray();

        $updatedPOSStagMenuSalesMaster = $this->pOSStagMenuSalesMasterRepo->update($fakePOSStagMenuSalesMaster, $pOSStagMenuSalesMaster->id);

        $this->assertModelData($fakePOSStagMenuSalesMaster, $updatedPOSStagMenuSalesMaster->toArray());
        $dbPOSStagMenuSalesMaster = $this->pOSStagMenuSalesMasterRepo->find($pOSStagMenuSalesMaster->id);
        $this->assertModelData($fakePOSStagMenuSalesMaster, $dbPOSStagMenuSalesMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_stag_menu_sales_master()
    {
        $pOSStagMenuSalesMaster = factory(POSStagMenuSalesMaster::class)->create();

        $resp = $this->pOSStagMenuSalesMasterRepo->delete($pOSStagMenuSalesMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(POSStagMenuSalesMaster::find($pOSStagMenuSalesMaster->id), 'POSStagMenuSalesMaster should not exist in DB');
    }
}
