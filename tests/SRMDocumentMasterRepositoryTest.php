<?php namespace Tests\Repositories;

use App\Models\SRMDocumentMaster;
use App\Repositories\SRMDocumentMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SRMDocumentMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SRMDocumentMasterRepository
     */
    protected $sRMDocumentMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sRMDocumentMasterRepo = \App::make(SRMDocumentMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_r_m_document_master()
    {
        $sRMDocumentMaster = factory(SRMDocumentMaster::class)->make()->toArray();

        $createdSRMDocumentMaster = $this->sRMDocumentMasterRepo->create($sRMDocumentMaster);

        $createdSRMDocumentMaster = $createdSRMDocumentMaster->toArray();
        $this->assertArrayHasKey('id', $createdSRMDocumentMaster);
        $this->assertNotNull($createdSRMDocumentMaster['id'], 'Created SRMDocumentMaster must have id specified');
        $this->assertNotNull(SRMDocumentMaster::find($createdSRMDocumentMaster['id']), 'SRMDocumentMaster with given id must be in DB');
        $this->assertModelData($sRMDocumentMaster, $createdSRMDocumentMaster);
    }

    /**
     * @test read
     */
    public function test_read_s_r_m_document_master()
    {
        $sRMDocumentMaster = factory(SRMDocumentMaster::class)->create();

        $dbSRMDocumentMaster = $this->sRMDocumentMasterRepo->find($sRMDocumentMaster->id);

        $dbSRMDocumentMaster = $dbSRMDocumentMaster->toArray();
        $this->assertModelData($sRMDocumentMaster->toArray(), $dbSRMDocumentMaster);
    }

    /**
     * @test update
     */
    public function test_update_s_r_m_document_master()
    {
        $sRMDocumentMaster = factory(SRMDocumentMaster::class)->create();
        $fakeSRMDocumentMaster = factory(SRMDocumentMaster::class)->make()->toArray();

        $updatedSRMDocumentMaster = $this->sRMDocumentMasterRepo->update($fakeSRMDocumentMaster, $sRMDocumentMaster->id);

        $this->assertModelData($fakeSRMDocumentMaster, $updatedSRMDocumentMaster->toArray());
        $dbSRMDocumentMaster = $this->sRMDocumentMasterRepo->find($sRMDocumentMaster->id);
        $this->assertModelData($fakeSRMDocumentMaster, $dbSRMDocumentMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_r_m_document_master()
    {
        $sRMDocumentMaster = factory(SRMDocumentMaster::class)->create();

        $resp = $this->sRMDocumentMasterRepo->delete($sRMDocumentMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(SRMDocumentMaster::find($sRMDocumentMaster->id), 'SRMDocumentMaster should not exist in DB');
    }
}
