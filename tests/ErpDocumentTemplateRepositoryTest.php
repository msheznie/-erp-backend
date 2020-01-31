<?php namespace Tests\Repositories;

use App\Models\ErpDocumentTemplate;
use App\Repositories\ErpDocumentTemplateRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeErpDocumentTemplateTrait;
use Tests\ApiTestTrait;

class ErpDocumentTemplateRepositoryTest extends TestCase
{
    use MakeErpDocumentTemplateTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ErpDocumentTemplateRepository
     */
    protected $erpDocumentTemplateRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->erpDocumentTemplateRepo = \App::make(ErpDocumentTemplateRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_erp_document_template()
    {
        $erpDocumentTemplate = $this->fakeErpDocumentTemplateData();
        $createdErpDocumentTemplate = $this->erpDocumentTemplateRepo->create($erpDocumentTemplate);
        $createdErpDocumentTemplate = $createdErpDocumentTemplate->toArray();
        $this->assertArrayHasKey('id', $createdErpDocumentTemplate);
        $this->assertNotNull($createdErpDocumentTemplate['id'], 'Created ErpDocumentTemplate must have id specified');
        $this->assertNotNull(ErpDocumentTemplate::find($createdErpDocumentTemplate['id']), 'ErpDocumentTemplate with given id must be in DB');
        $this->assertModelData($erpDocumentTemplate, $createdErpDocumentTemplate);
    }

    /**
     * @test read
     */
    public function test_read_erp_document_template()
    {
        $erpDocumentTemplate = $this->makeErpDocumentTemplate();
        $dbErpDocumentTemplate = $this->erpDocumentTemplateRepo->find($erpDocumentTemplate->id);
        $dbErpDocumentTemplate = $dbErpDocumentTemplate->toArray();
        $this->assertModelData($erpDocumentTemplate->toArray(), $dbErpDocumentTemplate);
    }

    /**
     * @test update
     */
    public function test_update_erp_document_template()
    {
        $erpDocumentTemplate = $this->makeErpDocumentTemplate();
        $fakeErpDocumentTemplate = $this->fakeErpDocumentTemplateData();
        $updatedErpDocumentTemplate = $this->erpDocumentTemplateRepo->update($fakeErpDocumentTemplate, $erpDocumentTemplate->id);
        $this->assertModelData($fakeErpDocumentTemplate, $updatedErpDocumentTemplate->toArray());
        $dbErpDocumentTemplate = $this->erpDocumentTemplateRepo->find($erpDocumentTemplate->id);
        $this->assertModelData($fakeErpDocumentTemplate, $dbErpDocumentTemplate->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_erp_document_template()
    {
        $erpDocumentTemplate = $this->makeErpDocumentTemplate();
        $resp = $this->erpDocumentTemplateRepo->delete($erpDocumentTemplate->id);
        $this->assertTrue($resp);
        $this->assertNull(ErpDocumentTemplate::find($erpDocumentTemplate->id), 'ErpDocumentTemplate should not exist in DB');
    }
}
