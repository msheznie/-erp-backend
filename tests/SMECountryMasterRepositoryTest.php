<?php namespace Tests\Repositories;

use App\Models\SMECountryMaster;
use App\Repositories\SMECountryMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMECountryMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMECountryMasterRepository
     */
    protected $sMECountryMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMECountryMasterRepo = \App::make(SMECountryMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_country_master()
    {
        $sMECountryMaster = factory(SMECountryMaster::class)->make()->toArray();

        $createdSMECountryMaster = $this->sMECountryMasterRepo->create($sMECountryMaster);

        $createdSMECountryMaster = $createdSMECountryMaster->toArray();
        $this->assertArrayHasKey('id', $createdSMECountryMaster);
        $this->assertNotNull($createdSMECountryMaster['id'], 'Created SMECountryMaster must have id specified');
        $this->assertNotNull(SMECountryMaster::find($createdSMECountryMaster['id']), 'SMECountryMaster with given id must be in DB');
        $this->assertModelData($sMECountryMaster, $createdSMECountryMaster);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_country_master()
    {
        $sMECountryMaster = factory(SMECountryMaster::class)->create();

        $dbSMECountryMaster = $this->sMECountryMasterRepo->find($sMECountryMaster->id);

        $dbSMECountryMaster = $dbSMECountryMaster->toArray();
        $this->assertModelData($sMECountryMaster->toArray(), $dbSMECountryMaster);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_country_master()
    {
        $sMECountryMaster = factory(SMECountryMaster::class)->create();
        $fakeSMECountryMaster = factory(SMECountryMaster::class)->make()->toArray();

        $updatedSMECountryMaster = $this->sMECountryMasterRepo->update($fakeSMECountryMaster, $sMECountryMaster->id);

        $this->assertModelData($fakeSMECountryMaster, $updatedSMECountryMaster->toArray());
        $dbSMECountryMaster = $this->sMECountryMasterRepo->find($sMECountryMaster->id);
        $this->assertModelData($fakeSMECountryMaster, $dbSMECountryMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_country_master()
    {
        $sMECountryMaster = factory(SMECountryMaster::class)->create();

        $resp = $this->sMECountryMasterRepo->delete($sMECountryMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(SMECountryMaster::find($sMECountryMaster->id), 'SMECountryMaster should not exist in DB');
    }
}
