<?php namespace Tests\Repositories;

use App\Models\SupplierEvaluationTemplate;
use App\Repositories\SupplierEvaluationTemplateRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SupplierEvaluationTemplateRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierEvaluationTemplateRepository
     */
    protected $supplierEvaluationTemplateRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->supplierEvaluationTemplateRepo = \App::make(SupplierEvaluationTemplateRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_supplier_evaluation_template()
    {
        $supplierEvaluationTemplate = factory(SupplierEvaluationTemplate::class)->make()->toArray();

        $createdSupplierEvaluationTemplate = $this->supplierEvaluationTemplateRepo->create($supplierEvaluationTemplate);

        $createdSupplierEvaluationTemplate = $createdSupplierEvaluationTemplate->toArray();
        $this->assertArrayHasKey('id', $createdSupplierEvaluationTemplate);
        $this->assertNotNull($createdSupplierEvaluationTemplate['id'], 'Created SupplierEvaluationTemplate must have id specified');
        $this->assertNotNull(SupplierEvaluationTemplate::find($createdSupplierEvaluationTemplate['id']), 'SupplierEvaluationTemplate with given id must be in DB');
        $this->assertModelData($supplierEvaluationTemplate, $createdSupplierEvaluationTemplate);
    }

    /**
     * @test read
     */
    public function test_read_supplier_evaluation_template()
    {
        $supplierEvaluationTemplate = factory(SupplierEvaluationTemplate::class)->create();

        $dbSupplierEvaluationTemplate = $this->supplierEvaluationTemplateRepo->find($supplierEvaluationTemplate->id);

        $dbSupplierEvaluationTemplate = $dbSupplierEvaluationTemplate->toArray();
        $this->assertModelData($supplierEvaluationTemplate->toArray(), $dbSupplierEvaluationTemplate);
    }

    /**
     * @test update
     */
    public function test_update_supplier_evaluation_template()
    {
        $supplierEvaluationTemplate = factory(SupplierEvaluationTemplate::class)->create();
        $fakeSupplierEvaluationTemplate = factory(SupplierEvaluationTemplate::class)->make()->toArray();

        $updatedSupplierEvaluationTemplate = $this->supplierEvaluationTemplateRepo->update($fakeSupplierEvaluationTemplate, $supplierEvaluationTemplate->id);

        $this->assertModelData($fakeSupplierEvaluationTemplate, $updatedSupplierEvaluationTemplate->toArray());
        $dbSupplierEvaluationTemplate = $this->supplierEvaluationTemplateRepo->find($supplierEvaluationTemplate->id);
        $this->assertModelData($fakeSupplierEvaluationTemplate, $dbSupplierEvaluationTemplate->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_supplier_evaluation_template()
    {
        $supplierEvaluationTemplate = factory(SupplierEvaluationTemplate::class)->create();

        $resp = $this->supplierEvaluationTemplateRepo->delete($supplierEvaluationTemplate->id);

        $this->assertTrue($resp);
        $this->assertNull(SupplierEvaluationTemplate::find($supplierEvaluationTemplate->id), 'SupplierEvaluationTemplate should not exist in DB');
    }
}
