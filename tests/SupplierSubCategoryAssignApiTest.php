<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierSubCategoryAssignApiTest extends TestCase
{
    use MakeSupplierSubCategoryAssignTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSupplierSubCategoryAssign()
    {
        $supplierSubCategoryAssign = $this->fakeSupplierSubCategoryAssignData();
        $this->json('POST', '/api/v1/supplierSubCategoryAssigns', $supplierSubCategoryAssign);

        $this->assertApiResponse($supplierSubCategoryAssign);
    }

    /**
     * @test
     */
    public function testReadSupplierSubCategoryAssign()
    {
        $supplierSubCategoryAssign = $this->makeSupplierSubCategoryAssign();
        $this->json('GET', '/api/v1/supplierSubCategoryAssigns/'.$supplierSubCategoryAssign->id);

        $this->assertApiResponse($supplierSubCategoryAssign->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSupplierSubCategoryAssign()
    {
        $supplierSubCategoryAssign = $this->makeSupplierSubCategoryAssign();
        $editedSupplierSubCategoryAssign = $this->fakeSupplierSubCategoryAssignData();

        $this->json('PUT', '/api/v1/supplierSubCategoryAssigns/'.$supplierSubCategoryAssign->id, $editedSupplierSubCategoryAssign);

        $this->assertApiResponse($editedSupplierSubCategoryAssign);
    }

    /**
     * @test
     */
    public function testDeleteSupplierSubCategoryAssign()
    {
        $supplierSubCategoryAssign = $this->makeSupplierSubCategoryAssign();
        $this->json('DELETE', '/api/v1/supplierSubCategoryAssigns/'.$supplierSubCategoryAssign->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/supplierSubCategoryAssigns/'.$supplierSubCategoryAssign->id);

        $this->assertResponseStatus(404);
    }
}
