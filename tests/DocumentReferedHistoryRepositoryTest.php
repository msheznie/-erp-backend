<?php

use App\Models\DocumentReferedHistory;
use App\Repositories\DocumentReferedHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentReferedHistoryRepositoryTest extends TestCase
{
    use MakeDocumentReferedHistoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentReferedHistoryRepository
     */
    protected $documentReferedHistoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->documentReferedHistoryRepo = App::make(DocumentReferedHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDocumentReferedHistory()
    {
        $documentReferedHistory = $this->fakeDocumentReferedHistoryData();
        $createdDocumentReferedHistory = $this->documentReferedHistoryRepo->create($documentReferedHistory);
        $createdDocumentReferedHistory = $createdDocumentReferedHistory->toArray();
        $this->assertArrayHasKey('id', $createdDocumentReferedHistory);
        $this->assertNotNull($createdDocumentReferedHistory['id'], 'Created DocumentReferedHistory must have id specified');
        $this->assertNotNull(DocumentReferedHistory::find($createdDocumentReferedHistory['id']), 'DocumentReferedHistory with given id must be in DB');
        $this->assertModelData($documentReferedHistory, $createdDocumentReferedHistory);
    }

    /**
     * @test read
     */
    public function testReadDocumentReferedHistory()
    {
        $documentReferedHistory = $this->makeDocumentReferedHistory();
        $dbDocumentReferedHistory = $this->documentReferedHistoryRepo->find($documentReferedHistory->id);
        $dbDocumentReferedHistory = $dbDocumentReferedHistory->toArray();
        $this->assertModelData($documentReferedHistory->toArray(), $dbDocumentReferedHistory);
    }

    /**
     * @test update
     */
    public function testUpdateDocumentReferedHistory()
    {
        $documentReferedHistory = $this->makeDocumentReferedHistory();
        $fakeDocumentReferedHistory = $this->fakeDocumentReferedHistoryData();
        $updatedDocumentReferedHistory = $this->documentReferedHistoryRepo->update($fakeDocumentReferedHistory, $documentReferedHistory->id);
        $this->assertModelData($fakeDocumentReferedHistory, $updatedDocumentReferedHistory->toArray());
        $dbDocumentReferedHistory = $this->documentReferedHistoryRepo->find($documentReferedHistory->id);
        $this->assertModelData($fakeDocumentReferedHistory, $dbDocumentReferedHistory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDocumentReferedHistory()
    {
        $documentReferedHistory = $this->makeDocumentReferedHistory();
        $resp = $this->documentReferedHistoryRepo->delete($documentReferedHistory->id);
        $this->assertTrue($resp);
        $this->assertNull(DocumentReferedHistory::find($documentReferedHistory->id), 'DocumentReferedHistory should not exist in DB');
    }
}
