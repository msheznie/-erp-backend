<?php

use App\Models\ReportTemplateEmployees;
use App\Repositories\ReportTemplateEmployeesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateEmployeesRepositoryTest extends TestCase
{
    use MakeReportTemplateEmployeesTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportTemplateEmployeesRepository
     */
    protected $reportTemplateEmployeesRepo;

    public function setUp()
    {
        parent::setUp();
        $this->reportTemplateEmployeesRepo = App::make(ReportTemplateEmployeesRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateReportTemplateEmployees()
    {
        $reportTemplateEmployees = $this->fakeReportTemplateEmployeesData();
        $createdReportTemplateEmployees = $this->reportTemplateEmployeesRepo->create($reportTemplateEmployees);
        $createdReportTemplateEmployees = $createdReportTemplateEmployees->toArray();
        $this->assertArrayHasKey('id', $createdReportTemplateEmployees);
        $this->assertNotNull($createdReportTemplateEmployees['id'], 'Created ReportTemplateEmployees must have id specified');
        $this->assertNotNull(ReportTemplateEmployees::find($createdReportTemplateEmployees['id']), 'ReportTemplateEmployees with given id must be in DB');
        $this->assertModelData($reportTemplateEmployees, $createdReportTemplateEmployees);
    }

    /**
     * @test read
     */
    public function testReadReportTemplateEmployees()
    {
        $reportTemplateEmployees = $this->makeReportTemplateEmployees();
        $dbReportTemplateEmployees = $this->reportTemplateEmployeesRepo->find($reportTemplateEmployees->id);
        $dbReportTemplateEmployees = $dbReportTemplateEmployees->toArray();
        $this->assertModelData($reportTemplateEmployees->toArray(), $dbReportTemplateEmployees);
    }

    /**
     * @test update
     */
    public function testUpdateReportTemplateEmployees()
    {
        $reportTemplateEmployees = $this->makeReportTemplateEmployees();
        $fakeReportTemplateEmployees = $this->fakeReportTemplateEmployeesData();
        $updatedReportTemplateEmployees = $this->reportTemplateEmployeesRepo->update($fakeReportTemplateEmployees, $reportTemplateEmployees->id);
        $this->assertModelData($fakeReportTemplateEmployees, $updatedReportTemplateEmployees->toArray());
        $dbReportTemplateEmployees = $this->reportTemplateEmployeesRepo->find($reportTemplateEmployees->id);
        $this->assertModelData($fakeReportTemplateEmployees, $dbReportTemplateEmployees->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteReportTemplateEmployees()
    {
        $reportTemplateEmployees = $this->makeReportTemplateEmployees();
        $resp = $this->reportTemplateEmployeesRepo->delete($reportTemplateEmployees->id);
        $this->assertTrue($resp);
        $this->assertNull(ReportTemplateEmployees::find($reportTemplateEmployees->id), 'ReportTemplateEmployees should not exist in DB');
    }
}
