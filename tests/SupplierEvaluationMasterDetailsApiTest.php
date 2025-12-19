<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SupplierEvaluationMasterDetails;

class SupplierEvaluationMasterDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_supplier_evaluation_master_details()
    {
        $supplierEvaluationMasterDetails = factory(SupplierEvaluationMasterDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/supplier_evaluation_master_details', $supplierEvaluationMasterDetails
        );

        $this->assertApiResponse($supplierEvaluationMasterDetails);
    }

    /**
     * @test
     */
    public function test_read_supplier_evaluation_master_details()
    {
        $supplierEvaluationMasterDetails = factory(SupplierEvaluationMasterDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/supplier_evaluation_master_details/'.$supplierEvaluationMasterDetails->id
        );

        $this->assertApiResponse($supplierEvaluationMasterDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_supplier_evaluation_master_details()
    {
        $supplierEvaluationMasterDetails = factory(SupplierEvaluationMasterDetails::class)->create();
        $editedSupplierEvaluationMasterDetails = factory(SupplierEvaluationMasterDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/supplier_evaluation_master_details/'.$supplierEvaluationMasterDetails->id,
            $editedSupplierEvaluationMasterDetails
        );

        $this->assertApiResponse($editedSupplierEvaluationMasterDetails);
    }

    /**
     * @test
     */
    public function test_delete_supplier_evaluation_master_details()
    {
        $supplierEvaluationMasterDetails = factory(SupplierEvaluationMasterDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/supplier_evaluation_master_details/'.$supplierEvaluationMasterDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/supplier_evaluation_master_details/'.$supplierEvaluationMasterDetails->id
        );

        $this->response->assertStatus(404);
    }
}
