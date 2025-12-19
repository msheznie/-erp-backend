<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierAssignedApiTest extends TestCase
{
    use MakeSupplierAssignedTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSupplierAssigned()
    {
        $supplierAssigned = $this->fakeSupplierAssignedData();
        $this->json('POST', '/api/v1/supplierAssigneds', $supplierAssigned);

        $this->assertApiResponse($supplierAssigned);
    }

    /**
     * @test
     */
    public function testReadSupplierAssigned()
    {
        $supplierAssigned = $this->makeSupplierAssigned();
        $this->json('GET', '/api/v1/supplierAssigneds/'.$supplierAssigned->id);

        $this->assertApiResponse($supplierAssigned->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSupplierAssigned()
    {
        $supplierAssigned = $this->makeSupplierAssigned();
        $editedSupplierAssigned = $this->fakeSupplierAssignedData();

        $this->json('PUT', '/api/v1/supplierAssigneds/'.$supplierAssigned->id, $editedSupplierAssigned);

        $this->assertApiResponse($editedSupplierAssigned);
    }

    /**
     * @test
     */
    public function testDeleteSupplierAssigned()
    {
        $supplierAssigned = $this->makeSupplierAssigned();
        $this->json('DELETE', '/api/v1/supplierAssigneds/'.$supplierAssigned->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/supplierAssigneds/'.$supplierAssigned->id);

        $this->assertResponseStatus(404);
    }
}
