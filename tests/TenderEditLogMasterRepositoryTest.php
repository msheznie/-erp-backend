<?php namespace Tests\Repositories;

use App\Models\TenderEditLogMaster;
use App\Repositories\TenderEditLogMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderEditLogMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderEditLogMasterRepository
     */
    protected $tenderEditLogMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderEditLogMasterRepo = \App::make(TenderEditLogMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_edit_log_master()
    {
        $tenderEditLogMaster = factory(TenderEditLogMaster::class)->make()->toArray();

        $createdTenderEditLogMaster = $this->tenderEditLogMasterRepo->create($tenderEditLogMaster);

        $createdTenderEditLogMaster = $createdTenderEditLogMaster->toArray();
        $this->assertArrayHasKey('id', $createdTenderEditLogMaster);
        $this->assertNotNull($createdTenderEditLogMaster['id'], 'Created TenderEditLogMaster must have id specified');
        $this->assertNotNull(TenderEditLogMaster::find($createdTenderEditLogMaster['id']), 'TenderEditLogMaster with given id must be in DB');
        $this->assertModelData($tenderEditLogMaster, $createdTenderEditLogMaster);
    }

    /**
     * @test read
     */
    public function test_read_tender_edit_log_master()
    {
        $tenderEditLogMaster = factory(TenderEditLogMaster::class)->create();

        $dbTenderEditLogMaster = $this->tenderEditLogMasterRepo->find($tenderEditLogMaster->id);

        $dbTenderEditLogMaster = $dbTenderEditLogMaster->toArray();
        $this->assertModelData($tenderEditLogMaster->toArray(), $dbTenderEditLogMaster);
    }

    /**
     * @test update
     */
    public function test_update_tender_edit_log_master()
    {
        $tenderEditLogMaster = factory(TenderEditLogMaster::class)->create();
        $fakeTenderEditLogMaster = factory(TenderEditLogMaster::class)->make()->toArray();

        $updatedTenderEditLogMaster = $this->tenderEditLogMasterRepo->update($fakeTenderEditLogMaster, $tenderEditLogMaster->id);

        $this->assertModelData($fakeTenderEditLogMaster, $updatedTenderEditLogMaster->toArray());
        $dbTenderEditLogMaster = $this->tenderEditLogMasterRepo->find($tenderEditLogMaster->id);
        $this->assertModelData($fakeTenderEditLogMaster, $dbTenderEditLogMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_edit_log_master()
    {
        $tenderEditLogMaster = factory(TenderEditLogMaster::class)->create();

        $resp = $this->tenderEditLogMasterRepo->delete($tenderEditLogMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderEditLogMaster::find($tenderEditLogMaster->id), 'TenderEditLogMaster should not exist in DB');
    }
}
