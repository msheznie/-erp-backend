<?php namespace Tests\Repositories;

use App\Models\ReportTemplateColumnsTranslations;
use App\Repositories\ReportTemplateColumnsTranslationsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ReportTemplateColumnsTranslationsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportTemplateColumnsTranslationsRepository
     */
    protected $reportTemplateColumnsTranslationsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->reportTemplateColumnsTranslationsRepo = \App::make(ReportTemplateColumnsTranslationsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_report_template_columns_translations()
    {
        $reportTemplateColumnsTranslations = factory(ReportTemplateColumnsTranslations::class)->make()->toArray();

        $createdReportTemplateColumnsTranslations = $this->reportTemplateColumnsTranslationsRepo->create($reportTemplateColumnsTranslations);

        $createdReportTemplateColumnsTranslations = $createdReportTemplateColumnsTranslations->toArray();
        $this->assertArrayHasKey('id', $createdReportTemplateColumnsTranslations);
        $this->assertNotNull($createdReportTemplateColumnsTranslations['id'], 'Created ReportTemplateColumnsTranslations must have id specified');
        $this->assertNotNull(ReportTemplateColumnsTranslations::find($createdReportTemplateColumnsTranslations['id']), 'ReportTemplateColumnsTranslations with given id must be in DB');
        $this->assertModelData($reportTemplateColumnsTranslations, $createdReportTemplateColumnsTranslations);
    }

    /**
     * @test read
     */
    public function test_read_report_template_columns_translations()
    {
        $reportTemplateColumnsTranslations = factory(ReportTemplateColumnsTranslations::class)->create();

        $dbReportTemplateColumnsTranslations = $this->reportTemplateColumnsTranslationsRepo->find($reportTemplateColumnsTranslations->id);

        $dbReportTemplateColumnsTranslations = $dbReportTemplateColumnsTranslations->toArray();
        $this->assertModelData($reportTemplateColumnsTranslations->toArray(), $dbReportTemplateColumnsTranslations);
    }

    /**
     * @test update
     */
    public function test_update_report_template_columns_translations()
    {
        $reportTemplateColumnsTranslations = factory(ReportTemplateColumnsTranslations::class)->create();
        $fakeReportTemplateColumnsTranslations = factory(ReportTemplateColumnsTranslations::class)->make()->toArray();

        $updatedReportTemplateColumnsTranslations = $this->reportTemplateColumnsTranslationsRepo->update($fakeReportTemplateColumnsTranslations, $reportTemplateColumnsTranslations->id);

        $this->assertModelData($fakeReportTemplateColumnsTranslations, $updatedReportTemplateColumnsTranslations->toArray());
        $dbReportTemplateColumnsTranslations = $this->reportTemplateColumnsTranslationsRepo->find($reportTemplateColumnsTranslations->id);
        $this->assertModelData($fakeReportTemplateColumnsTranslations, $dbReportTemplateColumnsTranslations->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_report_template_columns_translations()
    {
        $reportTemplateColumnsTranslations = factory(ReportTemplateColumnsTranslations::class)->create();

        $resp = $this->reportTemplateColumnsTranslationsRepo->delete($reportTemplateColumnsTranslations->id);

        $this->assertTrue($resp);
        $this->assertNull(ReportTemplateColumnsTranslations::find($reportTemplateColumnsTranslations->id), 'ReportTemplateColumnsTranslations should not exist in DB');
    }
}
