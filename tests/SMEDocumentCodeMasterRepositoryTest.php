<?php namespace Tests\Repositories;

use App\Models\SMEDocumentCodeMaster;
use App\Repositories\SMEDocumentCodeMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMEDocumentCodeMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMEDocumentCodeMasterRepository
     */
    protected $sMEDocumentCodeMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMEDocumentCodeMasterRepo = \App::make(SMEDocumentCodeMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_document_code_master()
    {
        $sMEDocumentCodeMaster = factory(SMEDocumentCodeMaster::class)->make()->toArray();

        $createdSMEDocumentCodeMaster = $this->sMEDocumentCodeMasterRepo->create($sMEDocumentCodeMaster);

        $createdSMEDocumentCodeMaster = $createdSMEDocumentCodeMaster->toArray();
        $this->assertArrayHasKey('id', $createdSMEDocumentCodeMaster);
        $this->assertNotNull($createdSMEDocumentCodeMaster['id'], 'Created SMEDocumentCodeMaster must have id specified');
        $this->assertNotNull(SMEDocumentCodeMaster::find($createdSMEDocumentCodeMaster['id']), 'SMEDocumentCodeMaster with given id must be in DB');
        $this->assertModelData($sMEDocumentCodeMaster, $createdSMEDocumentCodeMaster);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_document_code_master()
    {
        $sMEDocumentCodeMaster = factory(SMEDocumentCodeMaster::class)->create();

        $dbSMEDocumentCodeMaster = $this->sMEDocumentCodeMasterRepo->find($sMEDocumentCodeMaster->id);

        $dbSMEDocumentCodeMaster = $dbSMEDocumentCodeMaster->toArray();
        $this->assertModelData($sMEDocumentCodeMaster->toArray(), $dbSMEDocumentCodeMaster);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_document_code_master()
    {
        $sMEDocumentCodeMaster = factory(SMEDocumentCodeMaster::class)->create();
        $fakeSMEDocumentCodeMaster = factory(SMEDocumentCodeMaster::class)->make()->toArray();

        $updatedSMEDocumentCodeMaster = $this->sMEDocumentCodeMasterRepo->update($fakeSMEDocumentCodeMaster, $sMEDocumentCodeMaster->id);

        $this->assertModelData($fakeSMEDocumentCodeMaster, $updatedSMEDocumentCodeMaster->toArray());
        $dbSMEDocumentCodeMaster = $this->sMEDocumentCodeMasterRepo->find($sMEDocumentCodeMaster->id);
        $this->assertModelData($fakeSMEDocumentCodeMaster, $dbSMEDocumentCodeMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_document_code_master()
    {
        $sMEDocumentCodeMaster = factory(SMEDocumentCodeMaster::class)->create();

        $resp = $this->sMEDocumentCodeMasterRepo->delete($sMEDocumentCodeMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(SMEDocumentCodeMaster::find($sMEDocumentCodeMaster->id), 'SMEDocumentCodeMaster should not exist in DB');
    }
}
