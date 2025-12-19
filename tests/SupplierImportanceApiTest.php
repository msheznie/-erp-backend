<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierImportanceApiTest extends TestCase
{
    use MakeSupplierImportanceTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSupplierImportance()
    {
        $supplierImportance = $this->fakeSupplierImportanceData();
        $this->json('POST', '/api/v1/supplierImportances', $supplierImportance);

        $this->assertApiResponse($supplierImportance);
    }

    /**
     * @test
     */
    public function testReadSupplierImportance()
    {
        $supplierImportance = $this->makeSupplierImportance();
        $this->json('GET', '/api/v1/supplierImportances/'.$supplierImportance->id);

        $this->assertApiResponse($supplierImportance->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSupplierImportance()
    {
        $supplierImportance = $this->makeSupplierImportance();
        $editedSupplierImportance = $this->fakeSupplierImportanceData();

        $this->json('PUT', '/api/v1/supplierImportances/'.$supplierImportance->id, $editedSupplierImportance);

        $this->assertApiResponse($editedSupplierImportance);
    }

    /**
     * @test
     */
    public function testDeleteSupplierImportance()
    {
        $supplierImportance = $this->makeSupplierImportance();
        $this->json('DELETE', '/api/v1/supplierImportances/'.$supplierImportance->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/supplierImportances/'.$supplierImportance->id);

        $this->assertResponseStatus(404);
    }
}
