<?php namespace Tests\Repositories;

use App\Models\POSSOURCECustomerMaster;
use App\Repositories\POSSOURCECustomerMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSOURCECustomerMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSOURCECustomerMasterRepository
     */
    protected $pOSSOURCECustomerMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSOURCECustomerMasterRepo = \App::make(POSSOURCECustomerMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_s_o_u_r_c_e_customer_master()
    {
        $pOSSOURCECustomerMaster = factory(POSSOURCECustomerMaster::class)->make()->toArray();

        $createdPOSSOURCECustomerMaster = $this->pOSSOURCECustomerMasterRepo->create($pOSSOURCECustomerMaster);

        $createdPOSSOURCECustomerMaster = $createdPOSSOURCECustomerMaster->toArray();
        $this->assertArrayHasKey('id', $createdPOSSOURCECustomerMaster);
        $this->assertNotNull($createdPOSSOURCECustomerMaster['id'], 'Created POSSOURCECustomerMaster must have id specified');
        $this->assertNotNull(POSSOURCECustomerMaster::find($createdPOSSOURCECustomerMaster['id']), 'POSSOURCECustomerMaster with given id must be in DB');
        $this->assertModelData($pOSSOURCECustomerMaster, $createdPOSSOURCECustomerMaster);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_s_o_u_r_c_e_customer_master()
    {
        $pOSSOURCECustomerMaster = factory(POSSOURCECustomerMaster::class)->create();

        $dbPOSSOURCECustomerMaster = $this->pOSSOURCECustomerMasterRepo->find($pOSSOURCECustomerMaster->id);

        $dbPOSSOURCECustomerMaster = $dbPOSSOURCECustomerMaster->toArray();
        $this->assertModelData($pOSSOURCECustomerMaster->toArray(), $dbPOSSOURCECustomerMaster);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_s_o_u_r_c_e_customer_master()
    {
        $pOSSOURCECustomerMaster = factory(POSSOURCECustomerMaster::class)->create();
        $fakePOSSOURCECustomerMaster = factory(POSSOURCECustomerMaster::class)->make()->toArray();

        $updatedPOSSOURCECustomerMaster = $this->pOSSOURCECustomerMasterRepo->update($fakePOSSOURCECustomerMaster, $pOSSOURCECustomerMaster->id);

        $this->assertModelData($fakePOSSOURCECustomerMaster, $updatedPOSSOURCECustomerMaster->toArray());
        $dbPOSSOURCECustomerMaster = $this->pOSSOURCECustomerMasterRepo->find($pOSSOURCECustomerMaster->id);
        $this->assertModelData($fakePOSSOURCECustomerMaster, $dbPOSSOURCECustomerMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_s_o_u_r_c_e_customer_master()
    {
        $pOSSOURCECustomerMaster = factory(POSSOURCECustomerMaster::class)->create();

        $resp = $this->pOSSOURCECustomerMasterRepo->delete($pOSSOURCECustomerMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSOURCECustomerMaster::find($pOSSOURCECustomerMaster->id), 'POSSOURCECustomerMaster should not exist in DB');
    }
}
