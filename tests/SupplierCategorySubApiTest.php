<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierCategorySubApiTest extends TestCase
{
    use MakeSupplierCategorySubTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSupplierCategorySub()
    {
        $supplierCategorySub = $this->fakeSupplierCategorySubData();
        $this->json('POST', '/api/v1/supplierCategorySubs', $supplierCategorySub);

        $this->assertApiResponse($supplierCategorySub);
    }

    /**
     * @test
     */
    public function testReadSupplierCategorySub()
    {
        $supplierCategorySub = $this->makeSupplierCategorySub();
        $this->json('GET', '/api/v1/supplierCategorySubs/'.$supplierCategorySub->id);

        $this->assertApiResponse($supplierCategorySub->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSupplierCategorySub()
    {
        $supplierCategorySub = $this->makeSupplierCategorySub();
        $editedSupplierCategorySub = $this->fakeSupplierCategorySubData();

        $this->json('PUT', '/api/v1/supplierCategorySubs/'.$supplierCategorySub->id, $editedSupplierCategorySub);

        $this->assertApiResponse($editedSupplierCategorySub);
    }

    /**
     * @test
     */
    public function testDeleteSupplierCategorySub()
    {
        $supplierCategorySub = $this->makeSupplierCategorySub();
        $this->json('DELETE', '/api/v1/supplierCategorySubs/'.$supplierCategorySub->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/supplierCategorySubs/'.$supplierCategorySub->id);

        $this->assertResponseStatus(404);
    }
}
