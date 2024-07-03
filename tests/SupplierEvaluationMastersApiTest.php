<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SupplierEvaluationMasters;

class SupplierEvaluationMastersApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_supplier_evaluation_masters()
    {
        $supplierEvaluationMasters = factory(SupplierEvaluationMasters::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/supplier_evaluation_masters', $supplierEvaluationMasters
        );

        $this->assertApiResponse($supplierEvaluationMasters);
    }

    /**
     * @test
     */
    public function test_read_supplier_evaluation_masters()
    {
        $supplierEvaluationMasters = factory(SupplierEvaluationMasters::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/supplier_evaluation_masters/'.$supplierEvaluationMasters->id
        );

        $this->assertApiResponse($supplierEvaluationMasters->toArray());
    }

    /**
     * @test
     */
    public function test_update_supplier_evaluation_masters()
    {
        $supplierEvaluationMasters = factory(SupplierEvaluationMasters::class)->create();
        $editedSupplierEvaluationMasters = factory(SupplierEvaluationMasters::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/supplier_evaluation_masters/'.$supplierEvaluationMasters->id,
            $editedSupplierEvaluationMasters
        );

        $this->assertApiResponse($editedSupplierEvaluationMasters);
    }

    /**
     * @test
     */
    public function test_delete_supplier_evaluation_masters()
    {
        $supplierEvaluationMasters = factory(SupplierEvaluationMasters::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/supplier_evaluation_masters/'.$supplierEvaluationMasters->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/supplier_evaluation_masters/'.$supplierEvaluationMasters->id
        );

        $this->response->assertStatus(404);
    }
}
