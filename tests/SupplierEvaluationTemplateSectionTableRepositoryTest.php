<?php namespace Tests\Repositories;

use App\Models\SupplierEvaluationTemplateSectionTable;
use App\Repositories\SupplierEvaluationTemplateSectionTableRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SupplierEvaluationTemplateSectionTableRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierEvaluationTemplateSectionTableRepository
     */
    protected $supplierEvaluationTemplateSectionTableRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->supplierEvaluationTemplateSectionTableRepo = \App::make(SupplierEvaluationTemplateSectionTableRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_supplier_evaluation_template_section_table()
    {
        $supplierEvaluationTemplateSectionTable = factory(SupplierEvaluationTemplateSectionTable::class)->make()->toArray();

        $createdSupplierEvaluationTemplateSectionTable = $this->supplierEvaluationTemplateSectionTableRepo->create($supplierEvaluationTemplateSectionTable);

        $createdSupplierEvaluationTemplateSectionTable = $createdSupplierEvaluationTemplateSectionTable->toArray();
        $this->assertArrayHasKey('id', $createdSupplierEvaluationTemplateSectionTable);
        $this->assertNotNull($createdSupplierEvaluationTemplateSectionTable['id'], 'Created SupplierEvaluationTemplateSectionTable must have id specified');
        $this->assertNotNull(SupplierEvaluationTemplateSectionTable::find($createdSupplierEvaluationTemplateSectionTable['id']), 'SupplierEvaluationTemplateSectionTable with given id must be in DB');
        $this->assertModelData($supplierEvaluationTemplateSectionTable, $createdSupplierEvaluationTemplateSectionTable);
    }

    /**
     * @test read
     */
    public function test_read_supplier_evaluation_template_section_table()
    {
        $supplierEvaluationTemplateSectionTable = factory(SupplierEvaluationTemplateSectionTable::class)->create();

        $dbSupplierEvaluationTemplateSectionTable = $this->supplierEvaluationTemplateSectionTableRepo->find($supplierEvaluationTemplateSectionTable->id);

        $dbSupplierEvaluationTemplateSectionTable = $dbSupplierEvaluationTemplateSectionTable->toArray();
        $this->assertModelData($supplierEvaluationTemplateSectionTable->toArray(), $dbSupplierEvaluationTemplateSectionTable);
    }

    /**
     * @test update
     */
    public function test_update_supplier_evaluation_template_section_table()
    {
        $supplierEvaluationTemplateSectionTable = factory(SupplierEvaluationTemplateSectionTable::class)->create();
        $fakeSupplierEvaluationTemplateSectionTable = factory(SupplierEvaluationTemplateSectionTable::class)->make()->toArray();

        $updatedSupplierEvaluationTemplateSectionTable = $this->supplierEvaluationTemplateSectionTableRepo->update($fakeSupplierEvaluationTemplateSectionTable, $supplierEvaluationTemplateSectionTable->id);

        $this->assertModelData($fakeSupplierEvaluationTemplateSectionTable, $updatedSupplierEvaluationTemplateSectionTable->toArray());
        $dbSupplierEvaluationTemplateSectionTable = $this->supplierEvaluationTemplateSectionTableRepo->find($supplierEvaluationTemplateSectionTable->id);
        $this->assertModelData($fakeSupplierEvaluationTemplateSectionTable, $dbSupplierEvaluationTemplateSectionTable->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_supplier_evaluation_template_section_table()
    {
        $supplierEvaluationTemplateSectionTable = factory(SupplierEvaluationTemplateSectionTable::class)->create();

        $resp = $this->supplierEvaluationTemplateSectionTableRepo->delete($supplierEvaluationTemplateSectionTable->id);

        $this->assertTrue($resp);
        $this->assertNull(SupplierEvaluationTemplateSectionTable::find($supplierEvaluationTemplateSectionTable->id), 'SupplierEvaluationTemplateSectionTable should not exist in DB');
    }
}
