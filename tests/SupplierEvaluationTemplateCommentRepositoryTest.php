<?php namespace Tests\Repositories;

use App\Models\SupplierEvaluationTemplateComment;
use App\Repositories\SupplierEvaluationTemplateCommentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SupplierEvaluationTemplateCommentRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierEvaluationTemplateCommentRepository
     */
    protected $supplierEvaluationTemplateCommentRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->supplierEvaluationTemplateCommentRepo = \App::make(SupplierEvaluationTemplateCommentRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_supplier_evaluation_template_comment()
    {
        $supplierEvaluationTemplateComment = factory(SupplierEvaluationTemplateComment::class)->make()->toArray();

        $createdSupplierEvaluationTemplateComment = $this->supplierEvaluationTemplateCommentRepo->create($supplierEvaluationTemplateComment);

        $createdSupplierEvaluationTemplateComment = $createdSupplierEvaluationTemplateComment->toArray();
        $this->assertArrayHasKey('id', $createdSupplierEvaluationTemplateComment);
        $this->assertNotNull($createdSupplierEvaluationTemplateComment['id'], 'Created SupplierEvaluationTemplateComment must have id specified');
        $this->assertNotNull(SupplierEvaluationTemplateComment::find($createdSupplierEvaluationTemplateComment['id']), 'SupplierEvaluationTemplateComment with given id must be in DB');
        $this->assertModelData($supplierEvaluationTemplateComment, $createdSupplierEvaluationTemplateComment);
    }

    /**
     * @test read
     */
    public function test_read_supplier_evaluation_template_comment()
    {
        $supplierEvaluationTemplateComment = factory(SupplierEvaluationTemplateComment::class)->create();

        $dbSupplierEvaluationTemplateComment = $this->supplierEvaluationTemplateCommentRepo->find($supplierEvaluationTemplateComment->id);

        $dbSupplierEvaluationTemplateComment = $dbSupplierEvaluationTemplateComment->toArray();
        $this->assertModelData($supplierEvaluationTemplateComment->toArray(), $dbSupplierEvaluationTemplateComment);
    }

    /**
     * @test update
     */
    public function test_update_supplier_evaluation_template_comment()
    {
        $supplierEvaluationTemplateComment = factory(SupplierEvaluationTemplateComment::class)->create();
        $fakeSupplierEvaluationTemplateComment = factory(SupplierEvaluationTemplateComment::class)->make()->toArray();

        $updatedSupplierEvaluationTemplateComment = $this->supplierEvaluationTemplateCommentRepo->update($fakeSupplierEvaluationTemplateComment, $supplierEvaluationTemplateComment->id);

        $this->assertModelData($fakeSupplierEvaluationTemplateComment, $updatedSupplierEvaluationTemplateComment->toArray());
        $dbSupplierEvaluationTemplateComment = $this->supplierEvaluationTemplateCommentRepo->find($supplierEvaluationTemplateComment->id);
        $this->assertModelData($fakeSupplierEvaluationTemplateComment, $dbSupplierEvaluationTemplateComment->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_supplier_evaluation_template_comment()
    {
        $supplierEvaluationTemplateComment = factory(SupplierEvaluationTemplateComment::class)->create();

        $resp = $this->supplierEvaluationTemplateCommentRepo->delete($supplierEvaluationTemplateComment->id);

        $this->assertTrue($resp);
        $this->assertNull(SupplierEvaluationTemplateComment::find($supplierEvaluationTemplateComment->id), 'SupplierEvaluationTemplateComment should not exist in DB');
    }
}
