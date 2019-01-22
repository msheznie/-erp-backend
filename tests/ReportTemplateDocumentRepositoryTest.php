<?php

use App\Models\ReportTemplateDocument;
use App\Repositories\ReportTemplateDocumentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateDocumentRepositoryTest extends TestCase
{
    use MakeReportTemplateDocumentTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportTemplateDocumentRepository
     */
    protected $reportTemplateDocumentRepo;

    public function setUp()
    {
        parent::setUp();
        $this->reportTemplateDocumentRepo = App::make(ReportTemplateDocumentRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateReportTemplateDocument()
    {
        $reportTemplateDocument = $this->fakeReportTemplateDocumentData();
        $createdReportTemplateDocument = $this->reportTemplateDocumentRepo->create($reportTemplateDocument);
        $createdReportTemplateDocument = $createdReportTemplateDocument->toArray();
        $this->assertArrayHasKey('id', $createdReportTemplateDocument);
        $this->assertNotNull($createdReportTemplateDocument['id'], 'Created ReportTemplateDocument must have id specified');
        $this->assertNotNull(ReportTemplateDocument::find($createdReportTemplateDocument['id']), 'ReportTemplateDocument with given id must be in DB');
        $this->assertModelData($reportTemplateDocument, $createdReportTemplateDocument);
    }

    /**
     * @test read
     */
    public function testReadReportTemplateDocument()
    {
        $reportTemplateDocument = $this->makeReportTemplateDocument();
        $dbReportTemplateDocument = $this->reportTemplateDocumentRepo->find($reportTemplateDocument->id);
        $dbReportTemplateDocument = $dbReportTemplateDocument->toArray();
        $this->assertModelData($reportTemplateDocument->toArray(), $dbReportTemplateDocument);
    }

    /**
     * @test update
     */
    public function testUpdateReportTemplateDocument()
    {
        $reportTemplateDocument = $this->makeReportTemplateDocument();
        $fakeReportTemplateDocument = $this->fakeReportTemplateDocumentData();
        $updatedReportTemplateDocument = $this->reportTemplateDocumentRepo->update($fakeReportTemplateDocument, $reportTemplateDocument->id);
        $this->assertModelData($fakeReportTemplateDocument, $updatedReportTemplateDocument->toArray());
        $dbReportTemplateDocument = $this->reportTemplateDocumentRepo->find($reportTemplateDocument->id);
        $this->assertModelData($fakeReportTemplateDocument, $dbReportTemplateDocument->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteReportTemplateDocument()
    {
        $reportTemplateDocument = $this->makeReportTemplateDocument();
        $resp = $this->reportTemplateDocumentRepo->delete($reportTemplateDocument->id);
        $this->assertTrue($resp);
        $this->assertNull(ReportTemplateDocument::find($reportTemplateDocument->id), 'ReportTemplateDocument should not exist in DB');
    }
}
