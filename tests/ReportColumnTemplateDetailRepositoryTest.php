<?php namespace Tests\Repositories;

use App\Models\ReportColumnTemplateDetail;
use App\Repositories\ReportColumnTemplateDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeReportColumnTemplateDetailTrait;
use Tests\ApiTestTrait;

class ReportColumnTemplateDetailRepositoryTest extends TestCase
{
    use MakeReportColumnTemplateDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportColumnTemplateDetailRepository
     */
    protected $reportColumnTemplateDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->reportColumnTemplateDetailRepo = \App::make(ReportColumnTemplateDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_report_column_template_detail()
    {
        $reportColumnTemplateDetail = $this->fakeReportColumnTemplateDetailData();
        $createdReportColumnTemplateDetail = $this->reportColumnTemplateDetailRepo->create($reportColumnTemplateDetail);
        $createdReportColumnTemplateDetail = $createdReportColumnTemplateDetail->toArray();
        $this->assertArrayHasKey('id', $createdReportColumnTemplateDetail);
        $this->assertNotNull($createdReportColumnTemplateDetail['id'], 'Created ReportColumnTemplateDetail must have id specified');
        $this->assertNotNull(ReportColumnTemplateDetail::find($createdReportColumnTemplateDetail['id']), 'ReportColumnTemplateDetail with given id must be in DB');
        $this->assertModelData($reportColumnTemplateDetail, $createdReportColumnTemplateDetail);
    }

    /**
     * @test read
     */
    public function test_read_report_column_template_detail()
    {
        $reportColumnTemplateDetail = $this->makeReportColumnTemplateDetail();
        $dbReportColumnTemplateDetail = $this->reportColumnTemplateDetailRepo->find($reportColumnTemplateDetail->id);
        $dbReportColumnTemplateDetail = $dbReportColumnTemplateDetail->toArray();
        $this->assertModelData($reportColumnTemplateDetail->toArray(), $dbReportColumnTemplateDetail);
    }

    /**
     * @test update
     */
    public function test_update_report_column_template_detail()
    {
        $reportColumnTemplateDetail = $this->makeReportColumnTemplateDetail();
        $fakeReportColumnTemplateDetail = $this->fakeReportColumnTemplateDetailData();
        $updatedReportColumnTemplateDetail = $this->reportColumnTemplateDetailRepo->update($fakeReportColumnTemplateDetail, $reportColumnTemplateDetail->id);
        $this->assertModelData($fakeReportColumnTemplateDetail, $updatedReportColumnTemplateDetail->toArray());
        $dbReportColumnTemplateDetail = $this->reportColumnTemplateDetailRepo->find($reportColumnTemplateDetail->id);
        $this->assertModelData($fakeReportColumnTemplateDetail, $dbReportColumnTemplateDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_report_column_template_detail()
    {
        $reportColumnTemplateDetail = $this->makeReportColumnTemplateDetail();
        $resp = $this->reportColumnTemplateDetailRepo->delete($reportColumnTemplateDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(ReportColumnTemplateDetail::find($reportColumnTemplateDetail->id), 'ReportColumnTemplateDetail should not exist in DB');
    }
}
