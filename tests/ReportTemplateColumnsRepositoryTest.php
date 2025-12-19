<?php

use App\Models\ReportTemplateColumns;
use App\Repositories\ReportTemplateColumnsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateColumnsRepositoryTest extends TestCase
{
    use MakeReportTemplateColumnsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportTemplateColumnsRepository
     */
    protected $reportTemplateColumnsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->reportTemplateColumnsRepo = App::make(ReportTemplateColumnsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateReportTemplateColumns()
    {
        $reportTemplateColumns = $this->fakeReportTemplateColumnsData();
        $createdReportTemplateColumns = $this->reportTemplateColumnsRepo->create($reportTemplateColumns);
        $createdReportTemplateColumns = $createdReportTemplateColumns->toArray();
        $this->assertArrayHasKey('id', $createdReportTemplateColumns);
        $this->assertNotNull($createdReportTemplateColumns['id'], 'Created ReportTemplateColumns must have id specified');
        $this->assertNotNull(ReportTemplateColumns::find($createdReportTemplateColumns['id']), 'ReportTemplateColumns with given id must be in DB');
        $this->assertModelData($reportTemplateColumns, $createdReportTemplateColumns);
    }

    /**
     * @test read
     */
    public function testReadReportTemplateColumns()
    {
        $reportTemplateColumns = $this->makeReportTemplateColumns();
        $dbReportTemplateColumns = $this->reportTemplateColumnsRepo->find($reportTemplateColumns->id);
        $dbReportTemplateColumns = $dbReportTemplateColumns->toArray();
        $this->assertModelData($reportTemplateColumns->toArray(), $dbReportTemplateColumns);
    }

    /**
     * @test update
     */
    public function testUpdateReportTemplateColumns()
    {
        $reportTemplateColumns = $this->makeReportTemplateColumns();
        $fakeReportTemplateColumns = $this->fakeReportTemplateColumnsData();
        $updatedReportTemplateColumns = $this->reportTemplateColumnsRepo->update($fakeReportTemplateColumns, $reportTemplateColumns->id);
        $this->assertModelData($fakeReportTemplateColumns, $updatedReportTemplateColumns->toArray());
        $dbReportTemplateColumns = $this->reportTemplateColumnsRepo->find($reportTemplateColumns->id);
        $this->assertModelData($fakeReportTemplateColumns, $dbReportTemplateColumns->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteReportTemplateColumns()
    {
        $reportTemplateColumns = $this->makeReportTemplateColumns();
        $resp = $this->reportTemplateColumnsRepo->delete($reportTemplateColumns->id);
        $this->assertTrue($resp);
        $this->assertNull(ReportTemplateColumns::find($reportTemplateColumns->id), 'ReportTemplateColumns should not exist in DB');
    }
}
