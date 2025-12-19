<?php

use App\Models\ReportTemplateLinks;
use App\Repositories\ReportTemplateLinksRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateLinksRepositoryTest extends TestCase
{
    use MakeReportTemplateLinksTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportTemplateLinksRepository
     */
    protected $reportTemplateLinksRepo;

    public function setUp()
    {
        parent::setUp();
        $this->reportTemplateLinksRepo = App::make(ReportTemplateLinksRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateReportTemplateLinks()
    {
        $reportTemplateLinks = $this->fakeReportTemplateLinksData();
        $createdReportTemplateLinks = $this->reportTemplateLinksRepo->create($reportTemplateLinks);
        $createdReportTemplateLinks = $createdReportTemplateLinks->toArray();
        $this->assertArrayHasKey('id', $createdReportTemplateLinks);
        $this->assertNotNull($createdReportTemplateLinks['id'], 'Created ReportTemplateLinks must have id specified');
        $this->assertNotNull(ReportTemplateLinks::find($createdReportTemplateLinks['id']), 'ReportTemplateLinks with given id must be in DB');
        $this->assertModelData($reportTemplateLinks, $createdReportTemplateLinks);
    }

    /**
     * @test read
     */
    public function testReadReportTemplateLinks()
    {
        $reportTemplateLinks = $this->makeReportTemplateLinks();
        $dbReportTemplateLinks = $this->reportTemplateLinksRepo->find($reportTemplateLinks->id);
        $dbReportTemplateLinks = $dbReportTemplateLinks->toArray();
        $this->assertModelData($reportTemplateLinks->toArray(), $dbReportTemplateLinks);
    }

    /**
     * @test update
     */
    public function testUpdateReportTemplateLinks()
    {
        $reportTemplateLinks = $this->makeReportTemplateLinks();
        $fakeReportTemplateLinks = $this->fakeReportTemplateLinksData();
        $updatedReportTemplateLinks = $this->reportTemplateLinksRepo->update($fakeReportTemplateLinks, $reportTemplateLinks->id);
        $this->assertModelData($fakeReportTemplateLinks, $updatedReportTemplateLinks->toArray());
        $dbReportTemplateLinks = $this->reportTemplateLinksRepo->find($reportTemplateLinks->id);
        $this->assertModelData($fakeReportTemplateLinks, $dbReportTemplateLinks->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteReportTemplateLinks()
    {
        $reportTemplateLinks = $this->makeReportTemplateLinks();
        $resp = $this->reportTemplateLinksRepo->delete($reportTemplateLinks->id);
        $this->assertTrue($resp);
        $this->assertNull(ReportTemplateLinks::find($reportTemplateLinks->id), 'ReportTemplateLinks should not exist in DB');
    }
}
