<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierCriticalApiTest extends TestCase
{
    use MakeSupplierCriticalTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSupplierCritical()
    {
        $supplierCritical = $this->fakeSupplierCriticalData();
        $this->json('POST', '/api/v1/supplierCriticals', $supplierCritical);

        $this->assertApiResponse($supplierCritical);
    }

    /**
     * @test
     */
    public function testReadSupplierCritical()
    {
        $supplierCritical = $this->makeSupplierCritical();
        $this->json('GET', '/api/v1/supplierCriticals/'.$supplierCritical->id);

        $this->assertApiResponse($supplierCritical->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSupplierCritical()
    {
        $supplierCritical = $this->makeSupplierCritical();
        $editedSupplierCritical = $this->fakeSupplierCriticalData();

        $this->json('PUT', '/api/v1/supplierCriticals/'.$supplierCritical->id, $editedSupplierCritical);

        $this->assertApiResponse($editedSupplierCritical);
    }

    /**
     * @test
     */
    public function testDeleteSupplierCritical()
    {
        $supplierCritical = $this->makeSupplierCritical();
        $this->json('DELETE', '/api/v1/supplierCriticals/'.$supplierCritical->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/supplierCriticals/'.$supplierCritical->id);

        $this->assertResponseStatus(404);
    }
}
