<?php

use App\Models\ReportTemplateDetails;
use App\Repositories\ReportTemplateDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateDetailsRepositoryTest extends TestCase
{
    use MakeReportTemplateDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportTemplateDetailsRepository
     */
    protected $reportTemplateDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->reportTemplateDetailsRepo = App::make(ReportTemplateDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateReportTemplateDetails()
    {
        $reportTemplateDetails = $this->fakeReportTemplateDetailsData();
        $createdReportTemplateDetails = $this->reportTemplateDetailsRepo->create($reportTemplateDetails);
        $createdReportTemplateDetails = $createdReportTemplateDetails->toArray();
        $this->assertArrayHasKey('id', $createdReportTemplateDetails);
        $this->assertNotNull($createdReportTemplateDetails['id'], 'Created ReportTemplateDetails must have id specified');
        $this->assertNotNull(ReportTemplateDetails::find($createdReportTemplateDetails['id']), 'ReportTemplateDetails with given id must be in DB');
        $this->assertModelData($reportTemplateDetails, $createdReportTemplateDetails);
    }

    /**
     * @test read
     */
    public function testReadReportTemplateDetails()
    {
        $reportTemplateDetails = $this->makeReportTemplateDetails();
        $dbReportTemplateDetails = $this->reportTemplateDetailsRepo->find($reportTemplateDetails->id);
        $dbReportTemplateDetails = $dbReportTemplateDetails->toArray();
        $this->assertModelData($reportTemplateDetails->toArray(), $dbReportTemplateDetails);
    }

    /**
     * @test update
     */
    public function testUpdateReportTemplateDetails()
    {
        $reportTemplateDetails = $this->makeReportTemplateDetails();
        $fakeReportTemplateDetails = $this->fakeReportTemplateDetailsData();
        $updatedReportTemplateDetails = $this->reportTemplateDetailsRepo->update($fakeReportTemplateDetails, $reportTemplateDetails->id);
        $this->assertModelData($fakeReportTemplateDetails, $updatedReportTemplateDetails->toArray());
        $dbReportTemplateDetails = $this->reportTemplateDetailsRepo->find($reportTemplateDetails->id);
        $this->assertModelData($fakeReportTemplateDetails, $dbReportTemplateDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteReportTemplateDetails()
    {
        $reportTemplateDetails = $this->makeReportTemplateDetails();
        $resp = $this->reportTemplateDetailsRepo->delete($reportTemplateDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(ReportTemplateDetails::find($reportTemplateDetails->id), 'ReportTemplateDetails should not exist in DB');
    }
}
