<?php namespace Tests\Repositories;

use App\Models\SupplierEvaluationTemplateSectionTableColumn;
use App\Repositories\SupplierEvaluationTemplateSectionTableColumnRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SupplierEvaluationTemplateSectionTableColumnRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierEvaluationTemplateSectionTableColumnRepository
     */
    protected $supplierEvaluationTemplateSectionTableColumnRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->supplierEvaluationTemplateSectionTableColumnRepo = \App::make(SupplierEvaluationTemplateSectionTableColumnRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_supplier_evaluation_template_section_table_column()
    {
        $supplierEvaluationTemplateSectionTableColumn = factory(SupplierEvaluationTemplateSectionTableColumn::class)->make()->toArray();

        $createdSupplierEvaluationTemplateSectionTableColumn = $this->supplierEvaluationTemplateSectionTableColumnRepo->create($supplierEvaluationTemplateSectionTableColumn);

        $createdSupplierEvaluationTemplateSectionTableColumn = $createdSupplierEvaluationTemplateSectionTableColumn->toArray();
        $this->assertArrayHasKey('id', $createdSupplierEvaluationTemplateSectionTableColumn);
        $this->assertNotNull($createdSupplierEvaluationTemplateSectionTableColumn['id'], 'Created SupplierEvaluationTemplateSectionTableColumn must have id specified');
        $this->assertNotNull(SupplierEvaluationTemplateSectionTableColumn::find($createdSupplierEvaluationTemplateSectionTableColumn['id']), 'SupplierEvaluationTemplateSectionTableColumn with given id must be in DB');
        $this->assertModelData($supplierEvaluationTemplateSectionTableColumn, $createdSupplierEvaluationTemplateSectionTableColumn);
    }

    /**
     * @test read
     */
    public function test_read_supplier_evaluation_template_section_table_column()
    {
        $supplierEvaluationTemplateSectionTableColumn = factory(SupplierEvaluationTemplateSectionTableColumn::class)->create();

        $dbSupplierEvaluationTemplateSectionTableColumn = $this->supplierEvaluationTemplateSectionTableColumnRepo->find($supplierEvaluationTemplateSectionTableColumn->id);

        $dbSupplierEvaluationTemplateSectionTableColumn = $dbSupplierEvaluationTemplateSectionTableColumn->toArray();
        $this->assertModelData($supplierEvaluationTemplateSectionTableColumn->toArray(), $dbSupplierEvaluationTemplateSectionTableColumn);
    }

    /**
     * @test update
     */
    public function test_update_supplier_evaluation_template_section_table_column()
    {
        $supplierEvaluationTemplateSectionTableColumn = factory(SupplierEvaluationTemplateSectionTableColumn::class)->create();
        $fakeSupplierEvaluationTemplateSectionTableColumn = factory(SupplierEvaluationTemplateSectionTableColumn::class)->make()->toArray();

        $updatedSupplierEvaluationTemplateSectionTableColumn = $this->supplierEvaluationTemplateSectionTableColumnRepo->update($fakeSupplierEvaluationTemplateSectionTableColumn, $supplierEvaluationTemplateSectionTableColumn->id);

        $this->assertModelData($fakeSupplierEvaluationTemplateSectionTableColumn, $updatedSupplierEvaluationTemplateSectionTableColumn->toArray());
        $dbSupplierEvaluationTemplateSectionTableColumn = $this->supplierEvaluationTemplateSectionTableColumnRepo->find($supplierEvaluationTemplateSectionTableColumn->id);
        $this->assertModelData($fakeSupplierEvaluationTemplateSectionTableColumn, $dbSupplierEvaluationTemplateSectionTableColumn->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_supplier_evaluation_template_section_table_column()
    {
        $supplierEvaluationTemplateSectionTableColumn = factory(SupplierEvaluationTemplateSectionTableColumn::class)->create();

        $resp = $this->supplierEvaluationTemplateSectionTableColumnRepo->delete($supplierEvaluationTemplateSectionTableColumn->id);

        $this->assertTrue($resp);
        $this->assertNull(SupplierEvaluationTemplateSectionTableColumn::find($supplierEvaluationTemplateSectionTableColumn->id), 'SupplierEvaluationTemplateSectionTableColumn should not exist in DB');
    }
}
