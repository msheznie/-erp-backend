<?php

use App\Models\DocumentRestrictionAssign;
use App\Repositories\DocumentRestrictionAssignRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentRestrictionAssignRepositoryTest extends TestCase
{
    use MakeDocumentRestrictionAssignTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentRestrictionAssignRepository
     */
    protected $documentRestrictionAssignRepo;

    public function setUp()
    {
        parent::setUp();
        $this->documentRestrictionAssignRepo = App::make(DocumentRestrictionAssignRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDocumentRestrictionAssign()
    {
        $documentRestrictionAssign = $this->fakeDocumentRestrictionAssignData();
        $createdDocumentRestrictionAssign = $this->documentRestrictionAssignRepo->create($documentRestrictionAssign);
        $createdDocumentRestrictionAssign = $createdDocumentRestrictionAssign->toArray();
        $this->assertArrayHasKey('id', $createdDocumentRestrictionAssign);
        $this->assertNotNull($createdDocumentRestrictionAssign['id'], 'Created DocumentRestrictionAssign must have id specified');
        $this->assertNotNull(DocumentRestrictionAssign::find($createdDocumentRestrictionAssign['id']), 'DocumentRestrictionAssign with given id must be in DB');
        $this->assertModelData($documentRestrictionAssign, $createdDocumentRestrictionAssign);
    }

    /**
     * @test read
     */
    public function testReadDocumentRestrictionAssign()
    {
        $documentRestrictionAssign = $this->makeDocumentRestrictionAssign();
        $dbDocumentRestrictionAssign = $this->documentRestrictionAssignRepo->find($documentRestrictionAssign->id);
        $dbDocumentRestrictionAssign = $dbDocumentRestrictionAssign->toArray();
        $this->assertModelData($documentRestrictionAssign->toArray(), $dbDocumentRestrictionAssign);
    }

    /**
     * @test update
     */
    public function testUpdateDocumentRestrictionAssign()
    {
        $documentRestrictionAssign = $this->makeDocumentRestrictionAssign();
        $fakeDocumentRestrictionAssign = $this->fakeDocumentRestrictionAssignData();
        $updatedDocumentRestrictionAssign = $this->documentRestrictionAssignRepo->update($fakeDocumentRestrictionAssign, $documentRestrictionAssign->id);
        $this->assertModelData($fakeDocumentRestrictionAssign, $updatedDocumentRestrictionAssign->toArray());
        $dbDocumentRestrictionAssign = $this->documentRestrictionAssignRepo->find($documentRestrictionAssign->id);
        $this->assertModelData($fakeDocumentRestrictionAssign, $dbDocumentRestrictionAssign->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDocumentRestrictionAssign()
    {
        $documentRestrictionAssign = $this->makeDocumentRestrictionAssign();
        $resp = $this->documentRestrictionAssignRepo->delete($documentRestrictionAssign->id);
        $this->assertTrue($resp);
        $this->assertNull(DocumentRestrictionAssign::find($documentRestrictionAssign->id), 'DocumentRestrictionAssign should not exist in DB');
    }
}
