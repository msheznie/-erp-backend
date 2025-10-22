<?php namespace Tests\Repositories;

use App\Models\ReportCustomColumnTranslations;
use App\Repositories\ReportCustomColumnTranslationsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ReportCustomColumnTranslationsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportCustomColumnTranslationsRepository
     */
    protected $reportCustomColumnTranslationsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->reportCustomColumnTranslationsRepo = \App::make(ReportCustomColumnTranslationsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_report_custom_column_translations()
    {
        $reportCustomColumnTranslations = factory(ReportCustomColumnTranslations::class)->make()->toArray();

        $createdReportCustomColumnTranslations = $this->reportCustomColumnTranslationsRepo->create($reportCustomColumnTranslations);

        $createdReportCustomColumnTranslations = $createdReportCustomColumnTranslations->toArray();
        $this->assertArrayHasKey('id', $createdReportCustomColumnTranslations);
        $this->assertNotNull($createdReportCustomColumnTranslations['id'], 'Created ReportCustomColumnTranslations must have id specified');
        $this->assertNotNull(ReportCustomColumnTranslations::find($createdReportCustomColumnTranslations['id']), 'ReportCustomColumnTranslations with given id must be in DB');
        $this->assertModelData($reportCustomColumnTranslations, $createdReportCustomColumnTranslations);
    }

    /**
     * @test read
     */
    public function test_read_report_custom_column_translations()
    {
        $reportCustomColumnTranslations = factory(ReportCustomColumnTranslations::class)->create();

        $dbReportCustomColumnTranslations = $this->reportCustomColumnTranslationsRepo->find($reportCustomColumnTranslations->id);

        $dbReportCustomColumnTranslations = $dbReportCustomColumnTranslations->toArray();
        $this->assertModelData($reportCustomColumnTranslations->toArray(), $dbReportCustomColumnTranslations);
    }

    /**
     * @test update
     */
    public function test_update_report_custom_column_translations()
    {
        $reportCustomColumnTranslations = factory(ReportCustomColumnTranslations::class)->create();
        $fakeReportCustomColumnTranslations = factory(ReportCustomColumnTranslations::class)->make()->toArray();

        $updatedReportCustomColumnTranslations = $this->reportCustomColumnTranslationsRepo->update($fakeReportCustomColumnTranslations, $reportCustomColumnTranslations->id);

        $this->assertModelData($fakeReportCustomColumnTranslations, $updatedReportCustomColumnTranslations->toArray());
        $dbReportCustomColumnTranslations = $this->reportCustomColumnTranslationsRepo->find($reportCustomColumnTranslations->id);
        $this->assertModelData($fakeReportCustomColumnTranslations, $dbReportCustomColumnTranslations->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_report_custom_column_translations()
    {
        $reportCustomColumnTranslations = factory(ReportCustomColumnTranslations::class)->create();

        $resp = $this->reportCustomColumnTranslationsRepo->delete($reportCustomColumnTranslations->id);

        $this->assertTrue($resp);
        $this->assertNull(ReportCustomColumnTranslations::find($reportCustomColumnTranslations->id), 'ReportCustomColumnTranslations should not exist in DB');
    }
}
