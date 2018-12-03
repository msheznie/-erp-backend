<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierCategoryICVSubApiTest extends TestCase
{
    use MakeSupplierCategoryICVSubTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSupplierCategoryICVSub()
    {
        $supplierCategoryICVSub = $this->fakeSupplierCategoryICVSubData();
        $this->json('POST', '/api/v1/supplierCategoryICVSubs', $supplierCategoryICVSub);

        $this->assertApiResponse($supplierCategoryICVSub);
    }

    /**
     * @test
     */
    public function testReadSupplierCategoryICVSub()
    {
        $supplierCategoryICVSub = $this->makeSupplierCategoryICVSub();
        $this->json('GET', '/api/v1/supplierCategoryICVSubs/'.$supplierCategoryICVSub->id);

        $this->assertApiResponse($supplierCategoryICVSub->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSupplierCategoryICVSub()
    {
        $supplierCategoryICVSub = $this->makeSupplierCategoryICVSub();
        $editedSupplierCategoryICVSub = $this->fakeSupplierCategoryICVSubData();

        $this->json('PUT', '/api/v1/supplierCategoryICVSubs/'.$supplierCategoryICVSub->id, $editedSupplierCategoryICVSub);

        $this->assertApiResponse($editedSupplierCategoryICVSub);
    }

    /**
     * @test
     */
    public function testDeleteSupplierCategoryICVSub()
    {
        $supplierCategoryICVSub = $this->makeSupplierCategoryICVSub();
        $this->json('DELETE', '/api/v1/supplierCategoryICVSubs/'.$supplierCategoryICVSub->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/supplierCategoryICVSubs/'.$supplierCategoryICVSub->id);

        $this->assertResponseStatus(404);
    }
}
