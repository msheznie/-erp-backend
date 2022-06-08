<?php namespace Tests\Repositories;

use App\Models\TenderDocumentTypes;
use App\Repositories\TenderDocumentTypesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderDocumentTypesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderDocumentTypesRepository
     */
    protected $tenderDocumentTypesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderDocumentTypesRepo = \App::make(TenderDocumentTypesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_document_types()
    {
        $tenderDocumentTypes = factory(TenderDocumentTypes::class)->make()->toArray();

        $createdTenderDocumentTypes = $this->tenderDocumentTypesRepo->create($tenderDocumentTypes);

        $createdTenderDocumentTypes = $createdTenderDocumentTypes->toArray();
        $this->assertArrayHasKey('id', $createdTenderDocumentTypes);
        $this->assertNotNull($createdTenderDocumentTypes['id'], 'Created TenderDocumentTypes must have id specified');
        $this->assertNotNull(TenderDocumentTypes::find($createdTenderDocumentTypes['id']), 'TenderDocumentTypes with given id must be in DB');
        $this->assertModelData($tenderDocumentTypes, $createdTenderDocumentTypes);
    }

    /**
     * @test read
     */
    public function test_read_tender_document_types()
    {
        $tenderDocumentTypes = factory(TenderDocumentTypes::class)->create();

        $dbTenderDocumentTypes = $this->tenderDocumentTypesRepo->find($tenderDocumentTypes->id);

        $dbTenderDocumentTypes = $dbTenderDocumentTypes->toArray();
        $this->assertModelData($tenderDocumentTypes->toArray(), $dbTenderDocumentTypes);
    }

    /**
     * @test update
     */
    public function test_update_tender_document_types()
    {
        $tenderDocumentTypes = factory(TenderDocumentTypes::class)->create();
        $fakeTenderDocumentTypes = factory(TenderDocumentTypes::class)->make()->toArray();

        $updatedTenderDocumentTypes = $this->tenderDocumentTypesRepo->update($fakeTenderDocumentTypes, $tenderDocumentTypes->id);

        $this->assertModelData($fakeTenderDocumentTypes, $updatedTenderDocumentTypes->toArray());
        $dbTenderDocumentTypes = $this->tenderDocumentTypesRepo->find($tenderDocumentTypes->id);
        $this->assertModelData($fakeTenderDocumentTypes, $dbTenderDocumentTypes->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_document_types()
    {
        $tenderDocumentTypes = factory(TenderDocumentTypes::class)->create();

        $resp = $this->tenderDocumentTypesRepo->delete($tenderDocumentTypes->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderDocumentTypes::find($tenderDocumentTypes->id), 'TenderDocumentTypes should not exist in DB');
    }
}
