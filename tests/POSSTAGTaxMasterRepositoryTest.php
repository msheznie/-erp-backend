<?php namespace Tests\Repositories;

use App\Models\POSSTAGTaxMaster;
use App\Repositories\POSSTAGTaxMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSTAGTaxMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSTAGTaxMasterRepository
     */
    protected $pOSSTAGTaxMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSTAGTaxMasterRepo = \App::make(POSSTAGTaxMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_s_t_a_g_tax_master()
    {
        $pOSSTAGTaxMaster = factory(POSSTAGTaxMaster::class)->make()->toArray();

        $createdPOSSTAGTaxMaster = $this->pOSSTAGTaxMasterRepo->create($pOSSTAGTaxMaster);

        $createdPOSSTAGTaxMaster = $createdPOSSTAGTaxMaster->toArray();
        $this->assertArrayHasKey('id', $createdPOSSTAGTaxMaster);
        $this->assertNotNull($createdPOSSTAGTaxMaster['id'], 'Created POSSTAGTaxMaster must have id specified');
        $this->assertNotNull(POSSTAGTaxMaster::find($createdPOSSTAGTaxMaster['id']), 'POSSTAGTaxMaster with given id must be in DB');
        $this->assertModelData($pOSSTAGTaxMaster, $createdPOSSTAGTaxMaster);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_s_t_a_g_tax_master()
    {
        $pOSSTAGTaxMaster = factory(POSSTAGTaxMaster::class)->create();

        $dbPOSSTAGTaxMaster = $this->pOSSTAGTaxMasterRepo->find($pOSSTAGTaxMaster->id);

        $dbPOSSTAGTaxMaster = $dbPOSSTAGTaxMaster->toArray();
        $this->assertModelData($pOSSTAGTaxMaster->toArray(), $dbPOSSTAGTaxMaster);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_s_t_a_g_tax_master()
    {
        $pOSSTAGTaxMaster = factory(POSSTAGTaxMaster::class)->create();
        $fakePOSSTAGTaxMaster = factory(POSSTAGTaxMaster::class)->make()->toArray();

        $updatedPOSSTAGTaxMaster = $this->pOSSTAGTaxMasterRepo->update($fakePOSSTAGTaxMaster, $pOSSTAGTaxMaster->id);

        $this->assertModelData($fakePOSSTAGTaxMaster, $updatedPOSSTAGTaxMaster->toArray());
        $dbPOSSTAGTaxMaster = $this->pOSSTAGTaxMasterRepo->find($pOSSTAGTaxMaster->id);
        $this->assertModelData($fakePOSSTAGTaxMaster, $dbPOSSTAGTaxMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_s_t_a_g_tax_master()
    {
        $pOSSTAGTaxMaster = factory(POSSTAGTaxMaster::class)->create();

        $resp = $this->pOSSTAGTaxMasterRepo->delete($pOSSTAGTaxMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSTAGTaxMaster::find($pOSSTAGTaxMaster->id), 'POSSTAGTaxMaster should not exist in DB');
    }
}
