<?php

use App\Models\DocumentApproved;
use App\Repositories\DocumentApprovedRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentApprovedRepositoryTest extends TestCase
{
    use MakeDocumentApprovedTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentApprovedRepository
     */
    protected $documentApprovedRepo;

    public function setUp()
    {
        parent::setUp();
        $this->documentApprovedRepo = App::make(DocumentApprovedRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDocumentApproved()
    {
        $documentApproved = $this->fakeDocumentApprovedData();
        $createdDocumentApproved = $this->documentApprovedRepo->create($documentApproved);
        $createdDocumentApproved = $createdDocumentApproved->toArray();
        $this->assertArrayHasKey('id', $createdDocumentApproved);
        $this->assertNotNull($createdDocumentApproved['id'], 'Created DocumentApproved must have id specified');
        $this->assertNotNull(DocumentApproved::find($createdDocumentApproved['id']), 'DocumentApproved with given id must be in DB');
        $this->assertModelData($documentApproved, $createdDocumentApproved);
    }

    /**
     * @test read
     */
    public function testReadDocumentApproved()
    {
        $documentApproved = $this->makeDocumentApproved();
        $dbDocumentApproved = $this->documentApprovedRepo->find($documentApproved->id);
        $dbDocumentApproved = $dbDocumentApproved->toArray();
        $this->assertModelData($documentApproved->toArray(), $dbDocumentApproved);
    }

    /**
     * @test update
     */
    public function testUpdateDocumentApproved()
    {
        $documentApproved = $this->makeDocumentApproved();
        $fakeDocumentApproved = $this->fakeDocumentApprovedData();
        $updatedDocumentApproved = $this->documentApprovedRepo->update($fakeDocumentApproved, $documentApproved->id);
        $this->assertModelData($fakeDocumentApproved, $updatedDocumentApproved->toArray());
        $dbDocumentApproved = $this->documentApprovedRepo->find($documentApproved->id);
        $this->assertModelData($fakeDocumentApproved, $dbDocumentApproved->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDocumentApproved()
    {
        $documentApproved = $this->makeDocumentApproved();
        $resp = $this->documentApprovedRepo->delete($documentApproved->id);
        $this->assertTrue($resp);
        $this->assertNull(DocumentApproved::find($documentApproved->id), 'DocumentApproved should not exist in DB');
    }
}
