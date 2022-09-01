<?php namespace Tests\Repositories;

use App\Models\POSSTAGCustomerMaster;
use App\Repositories\POSSTAGCustomerMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSTAGCustomerMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSTAGCustomerMasterRepository
     */
    protected $pOSSTAGCustomerMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSTAGCustomerMasterRepo = \App::make(POSSTAGCustomerMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_s_t_a_g_customer_master()
    {
        $pOSSTAGCustomerMaster = factory(POSSTAGCustomerMaster::class)->make()->toArray();

        $createdPOSSTAGCustomerMaster = $this->pOSSTAGCustomerMasterRepo->create($pOSSTAGCustomerMaster);

        $createdPOSSTAGCustomerMaster = $createdPOSSTAGCustomerMaster->toArray();
        $this->assertArrayHasKey('id', $createdPOSSTAGCustomerMaster);
        $this->assertNotNull($createdPOSSTAGCustomerMaster['id'], 'Created POSSTAGCustomerMaster must have id specified');
        $this->assertNotNull(POSSTAGCustomerMaster::find($createdPOSSTAGCustomerMaster['id']), 'POSSTAGCustomerMaster with given id must be in DB');
        $this->assertModelData($pOSSTAGCustomerMaster, $createdPOSSTAGCustomerMaster);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_s_t_a_g_customer_master()
    {
        $pOSSTAGCustomerMaster = factory(POSSTAGCustomerMaster::class)->create();

        $dbPOSSTAGCustomerMaster = $this->pOSSTAGCustomerMasterRepo->find($pOSSTAGCustomerMaster->id);

        $dbPOSSTAGCustomerMaster = $dbPOSSTAGCustomerMaster->toArray();
        $this->assertModelData($pOSSTAGCustomerMaster->toArray(), $dbPOSSTAGCustomerMaster);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_s_t_a_g_customer_master()
    {
        $pOSSTAGCustomerMaster = factory(POSSTAGCustomerMaster::class)->create();
        $fakePOSSTAGCustomerMaster = factory(POSSTAGCustomerMaster::class)->make()->toArray();

        $updatedPOSSTAGCustomerMaster = $this->pOSSTAGCustomerMasterRepo->update($fakePOSSTAGCustomerMaster, $pOSSTAGCustomerMaster->id);

        $this->assertModelData($fakePOSSTAGCustomerMaster, $updatedPOSSTAGCustomerMaster->toArray());
        $dbPOSSTAGCustomerMaster = $this->pOSSTAGCustomerMasterRepo->find($pOSSTAGCustomerMaster->id);
        $this->assertModelData($fakePOSSTAGCustomerMaster, $dbPOSSTAGCustomerMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_s_t_a_g_customer_master()
    {
        $pOSSTAGCustomerMaster = factory(POSSTAGCustomerMaster::class)->create();

        $resp = $this->pOSSTAGCustomerMasterRepo->delete($pOSSTAGCustomerMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSTAGCustomerMaster::find($pOSSTAGCustomerMaster->id), 'POSSTAGCustomerMaster should not exist in DB');
    }
}
