<?php namespace Tests\Repositories;

use App\Models\POSSOURCETaxMaster;
use App\Repositories\POSSOURCETaxMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSOURCETaxMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSOURCETaxMasterRepository
     */
    protected $pOSSOURCETaxMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSOURCETaxMasterRepo = \App::make(POSSOURCETaxMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_s_o_u_r_c_e_tax_master()
    {
        $pOSSOURCETaxMaster = factory(POSSOURCETaxMaster::class)->make()->toArray();

        $createdPOSSOURCETaxMaster = $this->pOSSOURCETaxMasterRepo->create($pOSSOURCETaxMaster);

        $createdPOSSOURCETaxMaster = $createdPOSSOURCETaxMaster->toArray();
        $this->assertArrayHasKey('id', $createdPOSSOURCETaxMaster);
        $this->assertNotNull($createdPOSSOURCETaxMaster['id'], 'Created POSSOURCETaxMaster must have id specified');
        $this->assertNotNull(POSSOURCETaxMaster::find($createdPOSSOURCETaxMaster['id']), 'POSSOURCETaxMaster with given id must be in DB');
        $this->assertModelData($pOSSOURCETaxMaster, $createdPOSSOURCETaxMaster);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_s_o_u_r_c_e_tax_master()
    {
        $pOSSOURCETaxMaster = factory(POSSOURCETaxMaster::class)->create();

        $dbPOSSOURCETaxMaster = $this->pOSSOURCETaxMasterRepo->find($pOSSOURCETaxMaster->id);

        $dbPOSSOURCETaxMaster = $dbPOSSOURCETaxMaster->toArray();
        $this->assertModelData($pOSSOURCETaxMaster->toArray(), $dbPOSSOURCETaxMaster);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_s_o_u_r_c_e_tax_master()
    {
        $pOSSOURCETaxMaster = factory(POSSOURCETaxMaster::class)->create();
        $fakePOSSOURCETaxMaster = factory(POSSOURCETaxMaster::class)->make()->toArray();

        $updatedPOSSOURCETaxMaster = $this->pOSSOURCETaxMasterRepo->update($fakePOSSOURCETaxMaster, $pOSSOURCETaxMaster->id);

        $this->assertModelData($fakePOSSOURCETaxMaster, $updatedPOSSOURCETaxMaster->toArray());
        $dbPOSSOURCETaxMaster = $this->pOSSOURCETaxMasterRepo->find($pOSSOURCETaxMaster->id);
        $this->assertModelData($fakePOSSOURCETaxMaster, $dbPOSSOURCETaxMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_s_o_u_r_c_e_tax_master()
    {
        $pOSSOURCETaxMaster = factory(POSSOURCETaxMaster::class)->create();

        $resp = $this->pOSSOURCETaxMasterRepo->delete($pOSSOURCETaxMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSOURCETaxMaster::find($pOSSOURCETaxMaster->id), 'POSSOURCETaxMaster should not exist in DB');
    }
}
