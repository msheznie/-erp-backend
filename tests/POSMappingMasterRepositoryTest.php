<?php namespace Tests\Repositories;

use App\Models\POSMappingMaster;
use App\Repositories\POSMappingMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSMappingMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSMappingMasterRepository
     */
    protected $pOSMappingMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSMappingMasterRepo = \App::make(POSMappingMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_mapping_master()
    {
        $pOSMappingMaster = factory(POSMappingMaster::class)->make()->toArray();

        $createdPOSMappingMaster = $this->pOSMappingMasterRepo->create($pOSMappingMaster);

        $createdPOSMappingMaster = $createdPOSMappingMaster->toArray();
        $this->assertArrayHasKey('id', $createdPOSMappingMaster);
        $this->assertNotNull($createdPOSMappingMaster['id'], 'Created POSMappingMaster must have id specified');
        $this->assertNotNull(POSMappingMaster::find($createdPOSMappingMaster['id']), 'POSMappingMaster with given id must be in DB');
        $this->assertModelData($pOSMappingMaster, $createdPOSMappingMaster);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_mapping_master()
    {
        $pOSMappingMaster = factory(POSMappingMaster::class)->create();

        $dbPOSMappingMaster = $this->pOSMappingMasterRepo->find($pOSMappingMaster->id);

        $dbPOSMappingMaster = $dbPOSMappingMaster->toArray();
        $this->assertModelData($pOSMappingMaster->toArray(), $dbPOSMappingMaster);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_mapping_master()
    {
        $pOSMappingMaster = factory(POSMappingMaster::class)->create();
        $fakePOSMappingMaster = factory(POSMappingMaster::class)->make()->toArray();

        $updatedPOSMappingMaster = $this->pOSMappingMasterRepo->update($fakePOSMappingMaster, $pOSMappingMaster->id);

        $this->assertModelData($fakePOSMappingMaster, $updatedPOSMappingMaster->toArray());
        $dbPOSMappingMaster = $this->pOSMappingMasterRepo->find($pOSMappingMaster->id);
        $this->assertModelData($fakePOSMappingMaster, $dbPOSMappingMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_mapping_master()
    {
        $pOSMappingMaster = factory(POSMappingMaster::class)->create();

        $resp = $this->pOSMappingMasterRepo->delete($pOSMappingMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(POSMappingMaster::find($pOSMappingMaster->id), 'POSMappingMaster should not exist in DB');
    }
}
