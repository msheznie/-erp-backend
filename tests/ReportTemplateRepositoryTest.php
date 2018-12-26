<?php

use App\Models\ReportTemplate;
use App\Repositories\ReportTemplateRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateRepositoryTest extends TestCase
{
    use MakeReportTemplateTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportTemplateRepository
     */
    protected $reportTemplateRepo;

    public function setUp()
    {
        parent::setUp();
        $this->reportTemplateRepo = App::make(ReportTemplateRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateReportTemplate()
    {
        $reportTemplate = $this->fakeReportTemplateData();
        $createdReportTemplate = $this->reportTemplateRepo->create($reportTemplate);
        $createdReportTemplate = $createdReportTemplate->toArray();
        $this->assertArrayHasKey('id', $createdReportTemplate);
        $this->assertNotNull($createdReportTemplate['id'], 'Created ReportTemplate must have id specified');
        $this->assertNotNull(ReportTemplate::find($createdReportTemplate['id']), 'ReportTemplate with given id must be in DB');
        $this->assertModelData($reportTemplate, $createdReportTemplate);
    }

    /**
     * @test read
     */
    public function testReadReportTemplate()
    {
        $reportTemplate = $this->makeReportTemplate();
        $dbReportTemplate = $this->reportTemplateRepo->find($reportTemplate->id);
        $dbReportTemplate = $dbReportTemplate->toArray();
        $this->assertModelData($reportTemplate->toArray(), $dbReportTemplate);
    }

    /**
     * @test update
     */
    public function testUpdateReportTemplate()
    {
        $reportTemplate = $this->makeReportTemplate();
        $fakeReportTemplate = $this->fakeReportTemplateData();
        $updatedReportTemplate = $this->reportTemplateRepo->update($fakeReportTemplate, $reportTemplate->id);
        $this->assertModelData($fakeReportTemplate, $updatedReportTemplate->toArray());
        $dbReportTemplate = $this->reportTemplateRepo->find($reportTemplate->id);
        $this->assertModelData($fakeReportTemplate, $dbReportTemplate->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteReportTemplate()
    {
        $reportTemplate = $this->makeReportTemplate();
        $resp = $this->reportTemplateRepo->delete($reportTemplate->id);
        $this->assertTrue($resp);
        $this->assertNull(ReportTemplate::find($reportTemplate->id), 'ReportTemplate should not exist in DB');
    }
}
