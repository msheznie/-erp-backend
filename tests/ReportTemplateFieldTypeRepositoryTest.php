<?php

use App\Models\ReportTemplateFieldType;
use App\Repositories\ReportTemplateFieldTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateFieldTypeRepositoryTest extends TestCase
{
    use MakeReportTemplateFieldTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportTemplateFieldTypeRepository
     */
    protected $reportTemplateFieldTypeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->reportTemplateFieldTypeRepo = App::make(ReportTemplateFieldTypeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateReportTemplateFieldType()
    {
        $reportTemplateFieldType = $this->fakeReportTemplateFieldTypeData();
        $createdReportTemplateFieldType = $this->reportTemplateFieldTypeRepo->create($reportTemplateFieldType);
        $createdReportTemplateFieldType = $createdReportTemplateFieldType->toArray();
        $this->assertArrayHasKey('id', $createdReportTemplateFieldType);
        $this->assertNotNull($createdReportTemplateFieldType['id'], 'Created ReportTemplateFieldType must have id specified');
        $this->assertNotNull(ReportTemplateFieldType::find($createdReportTemplateFieldType['id']), 'ReportTemplateFieldType with given id must be in DB');
        $this->assertModelData($reportTemplateFieldType, $createdReportTemplateFieldType);
    }

    /**
     * @test read
     */
    public function testReadReportTemplateFieldType()
    {
        $reportTemplateFieldType = $this->makeReportTemplateFieldType();
        $dbReportTemplateFieldType = $this->reportTemplateFieldTypeRepo->find($reportTemplateFieldType->id);
        $dbReportTemplateFieldType = $dbReportTemplateFieldType->toArray();
        $this->assertModelData($reportTemplateFieldType->toArray(), $dbReportTemplateFieldType);
    }

    /**
     * @test update
     */
    public function testUpdateReportTemplateFieldType()
    {
        $reportTemplateFieldType = $this->makeReportTemplateFieldType();
        $fakeReportTemplateFieldType = $this->fakeReportTemplateFieldTypeData();
        $updatedReportTemplateFieldType = $this->reportTemplateFieldTypeRepo->update($fakeReportTemplateFieldType, $reportTemplateFieldType->id);
        $this->assertModelData($fakeReportTemplateFieldType, $updatedReportTemplateFieldType->toArray());
        $dbReportTemplateFieldType = $this->reportTemplateFieldTypeRepo->find($reportTemplateFieldType->id);
        $this->assertModelData($fakeReportTemplateFieldType, $dbReportTemplateFieldType->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteReportTemplateFieldType()
    {
        $reportTemplateFieldType = $this->makeReportTemplateFieldType();
        $resp = $this->reportTemplateFieldTypeRepo->delete($reportTemplateFieldType->id);
        $this->assertTrue($resp);
        $this->assertNull(ReportTemplateFieldType::find($reportTemplateFieldType->id), 'ReportTemplateFieldType should not exist in DB');
    }
}
