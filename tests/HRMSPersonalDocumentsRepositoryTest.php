<?php namespace Tests\Repositories;

use App\Models\HRMSPersonalDocuments;
use App\Repositories\HRMSPersonalDocumentsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeHRMSPersonalDocumentsTrait;
use Tests\ApiTestTrait;

class HRMSPersonalDocumentsRepositoryTest extends TestCase
{
    use MakeHRMSPersonalDocumentsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var HRMSPersonalDocumentsRepository
     */
    protected $hRMSPersonalDocumentsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hRMSPersonalDocumentsRepo = \App::make(HRMSPersonalDocumentsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_h_r_m_s_personal_documents()
    {
        $hRMSPersonalDocuments = $this->fakeHRMSPersonalDocumentsData();
        $createdHRMSPersonalDocuments = $this->hRMSPersonalDocumentsRepo->create($hRMSPersonalDocuments);
        $createdHRMSPersonalDocuments = $createdHRMSPersonalDocuments->toArray();
        $this->assertArrayHasKey('id', $createdHRMSPersonalDocuments);
        $this->assertNotNull($createdHRMSPersonalDocuments['id'], 'Created HRMSPersonalDocuments must have id specified');
        $this->assertNotNull(HRMSPersonalDocuments::find($createdHRMSPersonalDocuments['id']), 'HRMSPersonalDocuments with given id must be in DB');
        $this->assertModelData($hRMSPersonalDocuments, $createdHRMSPersonalDocuments);
    }

    /**
     * @test read
     */
    public function test_read_h_r_m_s_personal_documents()
    {
        $hRMSPersonalDocuments = $this->makeHRMSPersonalDocuments();
        $dbHRMSPersonalDocuments = $this->hRMSPersonalDocumentsRepo->find($hRMSPersonalDocuments->id);
        $dbHRMSPersonalDocuments = $dbHRMSPersonalDocuments->toArray();
        $this->assertModelData($hRMSPersonalDocuments->toArray(), $dbHRMSPersonalDocuments);
    }

    /**
     * @test update
     */
    public function test_update_h_r_m_s_personal_documents()
    {
        $hRMSPersonalDocuments = $this->makeHRMSPersonalDocuments();
        $fakeHRMSPersonalDocuments = $this->fakeHRMSPersonalDocumentsData();
        $updatedHRMSPersonalDocuments = $this->hRMSPersonalDocumentsRepo->update($fakeHRMSPersonalDocuments, $hRMSPersonalDocuments->id);
        $this->assertModelData($fakeHRMSPersonalDocuments, $updatedHRMSPersonalDocuments->toArray());
        $dbHRMSPersonalDocuments = $this->hRMSPersonalDocumentsRepo->find($hRMSPersonalDocuments->id);
        $this->assertModelData($fakeHRMSPersonalDocuments, $dbHRMSPersonalDocuments->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_h_r_m_s_personal_documents()
    {
        $hRMSPersonalDocuments = $this->makeHRMSPersonalDocuments();
        $resp = $this->hRMSPersonalDocumentsRepo->delete($hRMSPersonalDocuments->id);
        $this->assertTrue($resp);
        $this->assertNull(HRMSPersonalDocuments::find($hRMSPersonalDocuments->id), 'HRMSPersonalDocuments should not exist in DB');
    }
}
