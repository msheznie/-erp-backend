<?php namespace Tests\Repositories;

use App\Models\ReportTemplateEquity;
use App\Repositories\ReportTemplateEquityRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ReportTemplateEquityRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportTemplateEquityRepository
     */
    protected $reportTemplateEquityRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->reportTemplateEquityRepo = \App::make(ReportTemplateEquityRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_report_template_equity()
    {
        $reportTemplateEquity = factory(ReportTemplateEquity::class)->make()->toArray();

        $createdReportTemplateEquity = $this->reportTemplateEquityRepo->create($reportTemplateEquity);

        $createdReportTemplateEquity = $createdReportTemplateEquity->toArray();
        $this->assertArrayHasKey('id', $createdReportTemplateEquity);
        $this->assertNotNull($createdReportTemplateEquity['id'], 'Created ReportTemplateEquity must have id specified');
        $this->assertNotNull(ReportTemplateEquity::find($createdReportTemplateEquity['id']), 'ReportTemplateEquity with given id must be in DB');
        $this->assertModelData($reportTemplateEquity, $createdReportTemplateEquity);
    }

    /**
     * @test read
     */
    public function test_read_report_template_equity()
    {
        $reportTemplateEquity = factory(ReportTemplateEquity::class)->create();

        $dbReportTemplateEquity = $this->reportTemplateEquityRepo->find($reportTemplateEquity->id);

        $dbReportTemplateEquity = $dbReportTemplateEquity->toArray();
        $this->assertModelData($reportTemplateEquity->toArray(), $dbReportTemplateEquity);
    }

    /**
     * @test update
     */
    public function test_update_report_template_equity()
    {
        $reportTemplateEquity = factory(ReportTemplateEquity::class)->create();
        $fakeReportTemplateEquity = factory(ReportTemplateEquity::class)->make()->toArray();

        $updatedReportTemplateEquity = $this->reportTemplateEquityRepo->update($fakeReportTemplateEquity, $reportTemplateEquity->id);

        $this->assertModelData($fakeReportTemplateEquity, $updatedReportTemplateEquity->toArray());
        $dbReportTemplateEquity = $this->reportTemplateEquityRepo->find($reportTemplateEquity->id);
        $this->assertModelData($fakeReportTemplateEquity, $dbReportTemplateEquity->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_report_template_equity()
    {
        $reportTemplateEquity = factory(ReportTemplateEquity::class)->create();

        $resp = $this->reportTemplateEquityRepo->delete($reportTemplateEquity->id);

        $this->assertTrue($resp);
        $this->assertNull(ReportTemplateEquity::find($reportTemplateEquity->id), 'ReportTemplateEquity should not exist in DB');
    }
}
