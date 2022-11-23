<?php namespace Tests\Repositories;

use App\Models\TenderDocumentTypeAssign;
use App\Repositories\TenderDocumentTypeAssignRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TenderDocumentTypeAssignRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenderDocumentTypeAssignRepository
     */
    protected $tenderDocumentTypeAssignRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->tenderDocumentTypeAssignRepo = \App::make(TenderDocumentTypeAssignRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_tender_document_type_assign()
    {
        $tenderDocumentTypeAssign = factory(TenderDocumentTypeAssign::class)->make()->toArray();

        $createdTenderDocumentTypeAssign = $this->tenderDocumentTypeAssignRepo->create($tenderDocumentTypeAssign);

        $createdTenderDocumentTypeAssign = $createdTenderDocumentTypeAssign->toArray();
        $this->assertArrayHasKey('id', $createdTenderDocumentTypeAssign);
        $this->assertNotNull($createdTenderDocumentTypeAssign['id'], 'Created TenderDocumentTypeAssign must have id specified');
        $this->assertNotNull(TenderDocumentTypeAssign::find($createdTenderDocumentTypeAssign['id']), 'TenderDocumentTypeAssign with given id must be in DB');
        $this->assertModelData($tenderDocumentTypeAssign, $createdTenderDocumentTypeAssign);
    }

    /**
     * @test read
     */
    public function test_read_tender_document_type_assign()
    {
        $tenderDocumentTypeAssign = factory(TenderDocumentTypeAssign::class)->create();

        $dbTenderDocumentTypeAssign = $this->tenderDocumentTypeAssignRepo->find($tenderDocumentTypeAssign->id);

        $dbTenderDocumentTypeAssign = $dbTenderDocumentTypeAssign->toArray();
        $this->assertModelData($tenderDocumentTypeAssign->toArray(), $dbTenderDocumentTypeAssign);
    }

    /**
     * @test update
     */
    public function test_update_tender_document_type_assign()
    {
        $tenderDocumentTypeAssign = factory(TenderDocumentTypeAssign::class)->create();
        $fakeTenderDocumentTypeAssign = factory(TenderDocumentTypeAssign::class)->make()->toArray();

        $updatedTenderDocumentTypeAssign = $this->tenderDocumentTypeAssignRepo->update($fakeTenderDocumentTypeAssign, $tenderDocumentTypeAssign->id);

        $this->assertModelData($fakeTenderDocumentTypeAssign, $updatedTenderDocumentTypeAssign->toArray());
        $dbTenderDocumentTypeAssign = $this->tenderDocumentTypeAssignRepo->find($tenderDocumentTypeAssign->id);
        $this->assertModelData($fakeTenderDocumentTypeAssign, $dbTenderDocumentTypeAssign->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_tender_document_type_assign()
    {
        $tenderDocumentTypeAssign = factory(TenderDocumentTypeAssign::class)->create();

        $resp = $this->tenderDocumentTypeAssignRepo->delete($tenderDocumentTypeAssign->id);

        $this->assertTrue($resp);
        $this->assertNull(TenderDocumentTypeAssign::find($tenderDocumentTypeAssign->id), 'TenderDocumentTypeAssign should not exist in DB');
    }
}
