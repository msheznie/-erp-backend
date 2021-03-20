<?php namespace Tests\Repositories;

use App\Models\SMEOertimeGroupMaster;
use App\Repositories\SMEOertimeGroupMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMEOertimeGroupMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMEOertimeGroupMasterRepository
     */
    protected $sMEOertimeGroupMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMEOertimeGroupMasterRepo = \App::make(SMEOertimeGroupMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_oertime_group_master()
    {
        $sMEOertimeGroupMaster = factory(SMEOertimeGroupMaster::class)->make()->toArray();

        $createdSMEOertimeGroupMaster = $this->sMEOertimeGroupMasterRepo->create($sMEOertimeGroupMaster);

        $createdSMEOertimeGroupMaster = $createdSMEOertimeGroupMaster->toArray();
        $this->assertArrayHasKey('id', $createdSMEOertimeGroupMaster);
        $this->assertNotNull($createdSMEOertimeGroupMaster['id'], 'Created SMEOertimeGroupMaster must have id specified');
        $this->assertNotNull(SMEOertimeGroupMaster::find($createdSMEOertimeGroupMaster['id']), 'SMEOertimeGroupMaster with given id must be in DB');
        $this->assertModelData($sMEOertimeGroupMaster, $createdSMEOertimeGroupMaster);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_oertime_group_master()
    {
        $sMEOertimeGroupMaster = factory(SMEOertimeGroupMaster::class)->create();

        $dbSMEOertimeGroupMaster = $this->sMEOertimeGroupMasterRepo->find($sMEOertimeGroupMaster->id);

        $dbSMEOertimeGroupMaster = $dbSMEOertimeGroupMaster->toArray();
        $this->assertModelData($sMEOertimeGroupMaster->toArray(), $dbSMEOertimeGroupMaster);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_oertime_group_master()
    {
        $sMEOertimeGroupMaster = factory(SMEOertimeGroupMaster::class)->create();
        $fakeSMEOertimeGroupMaster = factory(SMEOertimeGroupMaster::class)->make()->toArray();

        $updatedSMEOertimeGroupMaster = $this->sMEOertimeGroupMasterRepo->update($fakeSMEOertimeGroupMaster, $sMEOertimeGroupMaster->id);

        $this->assertModelData($fakeSMEOertimeGroupMaster, $updatedSMEOertimeGroupMaster->toArray());
        $dbSMEOertimeGroupMaster = $this->sMEOertimeGroupMasterRepo->find($sMEOertimeGroupMaster->id);
        $this->assertModelData($fakeSMEOertimeGroupMaster, $dbSMEOertimeGroupMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_oertime_group_master()
    {
        $sMEOertimeGroupMaster = factory(SMEOertimeGroupMaster::class)->create();

        $resp = $this->sMEOertimeGroupMasterRepo->delete($sMEOertimeGroupMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(SMEOertimeGroupMaster::find($sMEOertimeGroupMaster->id), 'SMEOertimeGroupMaster should not exist in DB');
    }
}
