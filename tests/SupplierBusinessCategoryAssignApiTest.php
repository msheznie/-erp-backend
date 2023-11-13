<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SupplierBusinessCategoryAssign;

class SupplierBusinessCategoryAssignApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_supplier_business_category_assign()
    {
        $supplierBusinessCategoryAssign = factory(SupplierBusinessCategoryAssign::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/supplier_business_category_assigns', $supplierBusinessCategoryAssign
        );

        $this->assertApiResponse($supplierBusinessCategoryAssign);
    }

    /**
     * @test
     */
    public function test_read_supplier_business_category_assign()
    {
        $supplierBusinessCategoryAssign = factory(SupplierBusinessCategoryAssign::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/supplier_business_category_assigns/'.$supplierBusinessCategoryAssign->id
        );

        $this->assertApiResponse($supplierBusinessCategoryAssign->toArray());
    }

    /**
     * @test
     */
    public function test_update_supplier_business_category_assign()
    {
        $supplierBusinessCategoryAssign = factory(SupplierBusinessCategoryAssign::class)->create();
        $editedSupplierBusinessCategoryAssign = factory(SupplierBusinessCategoryAssign::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/supplier_business_category_assigns/'.$supplierBusinessCategoryAssign->id,
            $editedSupplierBusinessCategoryAssign
        );

        $this->assertApiResponse($editedSupplierBusinessCategoryAssign);
    }

    /**
     * @test
     */
    public function test_delete_supplier_business_category_assign()
    {
        $supplierBusinessCategoryAssign = factory(SupplierBusinessCategoryAssign::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/supplier_business_category_assigns/'.$supplierBusinessCategoryAssign->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/supplier_business_category_assigns/'.$supplierBusinessCategoryAssign->id
        );

        $this->response->assertStatus(404);
    }
}
