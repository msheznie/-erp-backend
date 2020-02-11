<?php namespace Tests\Repositories;

use App\Models\PreDefinedReportTemplate;
use App\Repositories\PreDefinedReportTemplateRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakePreDefinedReportTemplateTrait;
use Tests\ApiTestTrait;

class PreDefinedReportTemplateRepositoryTest extends TestCase
{
    use MakePreDefinedReportTemplateTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PreDefinedReportTemplateRepository
     */
    protected $preDefinedReportTemplateRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->preDefinedReportTemplateRepo = \App::make(PreDefinedReportTemplateRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_pre_defined_report_template()
    {
        $preDefinedReportTemplate = $this->fakePreDefinedReportTemplateData();
        $createdPreDefinedReportTemplate = $this->preDefinedReportTemplateRepo->create($preDefinedReportTemplate);
        $createdPreDefinedReportTemplate = $createdPreDefinedReportTemplate->toArray();
        $this->assertArrayHasKey('id', $createdPreDefinedReportTemplate);
        $this->assertNotNull($createdPreDefinedReportTemplate['id'], 'Created PreDefinedReportTemplate must have id specified');
        $this->assertNotNull(PreDefinedReportTemplate::find($createdPreDefinedReportTemplate['id']), 'PreDefinedReportTemplate with given id must be in DB');
        $this->assertModelData($preDefinedReportTemplate, $createdPreDefinedReportTemplate);
    }

    /**
     * @test read
     */
    public function test_read_pre_defined_report_template()
    {
        $preDefinedReportTemplate = $this->makePreDefinedReportTemplate();
        $dbPreDefinedReportTemplate = $this->preDefinedReportTemplateRepo->find($preDefinedReportTemplate->id);
        $dbPreDefinedReportTemplate = $dbPreDefinedReportTemplate->toArray();
        $this->assertModelData($preDefinedReportTemplate->toArray(), $dbPreDefinedReportTemplate);
    }

    /**
     * @test update
     */
    public function test_update_pre_defined_report_template()
    {
        $preDefinedReportTemplate = $this->makePreDefinedReportTemplate();
        $fakePreDefinedReportTemplate = $this->fakePreDefinedReportTemplateData();
        $updatedPreDefinedReportTemplate = $this->preDefinedReportTemplateRepo->update($fakePreDefinedReportTemplate, $preDefinedReportTemplate->id);
        $this->assertModelData($fakePreDefinedReportTemplate, $updatedPreDefinedReportTemplate->toArray());
        $dbPreDefinedReportTemplate = $this->preDefinedReportTemplateRepo->find($preDefinedReportTemplate->id);
        $this->assertModelData($fakePreDefinedReportTemplate, $dbPreDefinedReportTemplate->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_pre_defined_report_template()
    {
        $preDefinedReportTemplate = $this->makePreDefinedReportTemplate();
        $resp = $this->preDefinedReportTemplateRepo->delete($preDefinedReportTemplate->id);
        $this->assertTrue($resp);
        $this->assertNull(PreDefinedReportTemplate::find($preDefinedReportTemplate->id), 'PreDefinedReportTemplate should not exist in DB');
    }
}
