<?php namespace Tests\Repositories;

use App\Models\ReportColumnTemplate;
use App\Repositories\ReportColumnTemplateRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeReportColumnTemplateTrait;
use Tests\ApiTestTrait;

class ReportColumnTemplateRepositoryTest extends TestCase
{
    use MakeReportColumnTemplateTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportColumnTemplateRepository
     */
    protected $reportColumnTemplateRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->reportColumnTemplateRepo = \App::make(ReportColumnTemplateRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_report_column_template()
    {
        $reportColumnTemplate = $this->fakeReportColumnTemplateData();
        $createdReportColumnTemplate = $this->reportColumnTemplateRepo->create($reportColumnTemplate);
        $createdReportColumnTemplate = $createdReportColumnTemplate->toArray();
        $this->assertArrayHasKey('id', $createdReportColumnTemplate);
        $this->assertNotNull($createdReportColumnTemplate['id'], 'Created ReportColumnTemplate must have id specified');
        $this->assertNotNull(ReportColumnTemplate::find($createdReportColumnTemplate['id']), 'ReportColumnTemplate with given id must be in DB');
        $this->assertModelData($reportColumnTemplate, $createdReportColumnTemplate);
    }

    /**
     * @test read
     */
    public function test_read_report_column_template()
    {
        $reportColumnTemplate = $this->makeReportColumnTemplate();
        $dbReportColumnTemplate = $this->reportColumnTemplateRepo->find($reportColumnTemplate->id);
        $dbReportColumnTemplate = $dbReportColumnTemplate->toArray();
        $this->assertModelData($reportColumnTemplate->toArray(), $dbReportColumnTemplate);
    }

    /**
     * @test update
     */
    public function test_update_report_column_template()
    {
        $reportColumnTemplate = $this->makeReportColumnTemplate();
        $fakeReportColumnTemplate = $this->fakeReportColumnTemplateData();
        $updatedReportColumnTemplate = $this->reportColumnTemplateRepo->update($fakeReportColumnTemplate, $reportColumnTemplate->id);
        $this->assertModelData($fakeReportColumnTemplate, $updatedReportColumnTemplate->toArray());
        $dbReportColumnTemplate = $this->reportColumnTemplateRepo->find($reportColumnTemplate->id);
        $this->assertModelData($fakeReportColumnTemplate, $dbReportColumnTemplate->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_report_column_template()
    {
        $reportColumnTemplate = $this->makeReportColumnTemplate();
        $resp = $this->reportColumnTemplateRepo->delete($reportColumnTemplate->id);
        $this->assertTrue($resp);
        $this->assertNull(ReportColumnTemplate::find($reportColumnTemplate->id), 'ReportColumnTemplate should not exist in DB');
    }
}
