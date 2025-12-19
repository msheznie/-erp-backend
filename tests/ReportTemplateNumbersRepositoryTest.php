<?php

use App\Models\ReportTemplateNumbers;
use App\Repositories\ReportTemplateNumbersRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateNumbersRepositoryTest extends TestCase
{
    use MakeReportTemplateNumbersTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportTemplateNumbersRepository
     */
    protected $reportTemplateNumbersRepo;

    public function setUp()
    {
        parent::setUp();
        $this->reportTemplateNumbersRepo = App::make(ReportTemplateNumbersRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateReportTemplateNumbers()
    {
        $reportTemplateNumbers = $this->fakeReportTemplateNumbersData();
        $createdReportTemplateNumbers = $this->reportTemplateNumbersRepo->create($reportTemplateNumbers);
        $createdReportTemplateNumbers = $createdReportTemplateNumbers->toArray();
        $this->assertArrayHasKey('id', $createdReportTemplateNumbers);
        $this->assertNotNull($createdReportTemplateNumbers['id'], 'Created ReportTemplateNumbers must have id specified');
        $this->assertNotNull(ReportTemplateNumbers::find($createdReportTemplateNumbers['id']), 'ReportTemplateNumbers with given id must be in DB');
        $this->assertModelData($reportTemplateNumbers, $createdReportTemplateNumbers);
    }

    /**
     * @test read
     */
    public function testReadReportTemplateNumbers()
    {
        $reportTemplateNumbers = $this->makeReportTemplateNumbers();
        $dbReportTemplateNumbers = $this->reportTemplateNumbersRepo->find($reportTemplateNumbers->id);
        $dbReportTemplateNumbers = $dbReportTemplateNumbers->toArray();
        $this->assertModelData($reportTemplateNumbers->toArray(), $dbReportTemplateNumbers);
    }

    /**
     * @test update
     */
    public function testUpdateReportTemplateNumbers()
    {
        $reportTemplateNumbers = $this->makeReportTemplateNumbers();
        $fakeReportTemplateNumbers = $this->fakeReportTemplateNumbersData();
        $updatedReportTemplateNumbers = $this->reportTemplateNumbersRepo->update($fakeReportTemplateNumbers, $reportTemplateNumbers->id);
        $this->assertModelData($fakeReportTemplateNumbers, $updatedReportTemplateNumbers->toArray());
        $dbReportTemplateNumbers = $this->reportTemplateNumbersRepo->find($reportTemplateNumbers->id);
        $this->assertModelData($fakeReportTemplateNumbers, $dbReportTemplateNumbers->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteReportTemplateNumbers()
    {
        $reportTemplateNumbers = $this->makeReportTemplateNumbers();
        $resp = $this->reportTemplateNumbersRepo->delete($reportTemplateNumbers->id);
        $this->assertTrue($resp);
        $this->assertNull(ReportTemplateNumbers::find($reportTemplateNumbers->id), 'ReportTemplateNumbers should not exist in DB');
    }
}
