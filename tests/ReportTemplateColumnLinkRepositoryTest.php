<?php

use App\Models\ReportTemplateColumnLink;
use App\Repositories\ReportTemplateColumnLinkRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateColumnLinkRepositoryTest extends TestCase
{
    use MakeReportTemplateColumnLinkTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportTemplateColumnLinkRepository
     */
    protected $reportTemplateColumnLinkRepo;

    public function setUp()
    {
        parent::setUp();
        $this->reportTemplateColumnLinkRepo = App::make(ReportTemplateColumnLinkRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateReportTemplateColumnLink()
    {
        $reportTemplateColumnLink = $this->fakeReportTemplateColumnLinkData();
        $createdReportTemplateColumnLink = $this->reportTemplateColumnLinkRepo->create($reportTemplateColumnLink);
        $createdReportTemplateColumnLink = $createdReportTemplateColumnLink->toArray();
        $this->assertArrayHasKey('id', $createdReportTemplateColumnLink);
        $this->assertNotNull($createdReportTemplateColumnLink['id'], 'Created ReportTemplateColumnLink must have id specified');
        $this->assertNotNull(ReportTemplateColumnLink::find($createdReportTemplateColumnLink['id']), 'ReportTemplateColumnLink with given id must be in DB');
        $this->assertModelData($reportTemplateColumnLink, $createdReportTemplateColumnLink);
    }

    /**
     * @test read
     */
    public function testReadReportTemplateColumnLink()
    {
        $reportTemplateColumnLink = $this->makeReportTemplateColumnLink();
        $dbReportTemplateColumnLink = $this->reportTemplateColumnLinkRepo->find($reportTemplateColumnLink->id);
        $dbReportTemplateColumnLink = $dbReportTemplateColumnLink->toArray();
        $this->assertModelData($reportTemplateColumnLink->toArray(), $dbReportTemplateColumnLink);
    }

    /**
     * @test update
     */
    public function testUpdateReportTemplateColumnLink()
    {
        $reportTemplateColumnLink = $this->makeReportTemplateColumnLink();
        $fakeReportTemplateColumnLink = $this->fakeReportTemplateColumnLinkData();
        $updatedReportTemplateColumnLink = $this->reportTemplateColumnLinkRepo->update($fakeReportTemplateColumnLink, $reportTemplateColumnLink->id);
        $this->assertModelData($fakeReportTemplateColumnLink, $updatedReportTemplateColumnLink->toArray());
        $dbReportTemplateColumnLink = $this->reportTemplateColumnLinkRepo->find($reportTemplateColumnLink->id);
        $this->assertModelData($fakeReportTemplateColumnLink, $dbReportTemplateColumnLink->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteReportTemplateColumnLink()
    {
        $reportTemplateColumnLink = $this->makeReportTemplateColumnLink();
        $resp = $this->reportTemplateColumnLinkRepo->delete($reportTemplateColumnLink->id);
        $this->assertTrue($resp);
        $this->assertNull(ReportTemplateColumnLink::find($reportTemplateColumnLink->id), 'ReportTemplateColumnLink should not exist in DB');
    }
}
