<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierMasterApiTest extends TestCase
{
    use MakeSupplierMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSupplierMaster()
    {
        $supplierMaster = $this->fakeSupplierMasterData();
        $this->json('POST', '/api/v1/supplierMasters', $supplierMaster);

        $this->assertApiResponse($supplierMaster);
    }

    /**
     * @test
     */
    public function testReadSupplierMaster()
    {
        $supplierMaster = $this->makeSupplierMaster();
        $this->json('GET', '/api/v1/supplierMasters/'.$supplierMaster->id);

        $this->assertApiResponse($supplierMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSupplierMaster()
    {
        $supplierMaster = $this->makeSupplierMaster();
        $editedSupplierMaster = $this->fakeSupplierMasterData();

        $this->json('PUT', '/api/v1/supplierMasters/'.$supplierMaster->id, $editedSupplierMaster);

        $this->assertApiResponse($editedSupplierMaster);
    }

    /**
     * @test
     */
    public function testDeleteSupplierMaster()
    {
        $supplierMaster = $this->makeSupplierMaster();
        $this->json('DELETE', '/api/v1/supplierMasters/'.$supplierMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/supplierMasters/'.$supplierMaster->id);

        $this->assertResponseStatus(404);
    }
}
