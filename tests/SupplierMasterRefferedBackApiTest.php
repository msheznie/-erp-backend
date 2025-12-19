<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierMasterRefferedBackApiTest extends TestCase
{
    use MakeSupplierMasterRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSupplierMasterRefferedBack()
    {
        $supplierMasterRefferedBack = $this->fakeSupplierMasterRefferedBackData();
        $this->json('POST', '/api/v1/supplierMasterRefferedBacks', $supplierMasterRefferedBack);

        $this->assertApiResponse($supplierMasterRefferedBack);
    }

    /**
     * @test
     */
    public function testReadSupplierMasterRefferedBack()
    {
        $supplierMasterRefferedBack = $this->makeSupplierMasterRefferedBack();
        $this->json('GET', '/api/v1/supplierMasterRefferedBacks/'.$supplierMasterRefferedBack->id);

        $this->assertApiResponse($supplierMasterRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSupplierMasterRefferedBack()
    {
        $supplierMasterRefferedBack = $this->makeSupplierMasterRefferedBack();
        $editedSupplierMasterRefferedBack = $this->fakeSupplierMasterRefferedBackData();

        $this->json('PUT', '/api/v1/supplierMasterRefferedBacks/'.$supplierMasterRefferedBack->id, $editedSupplierMasterRefferedBack);

        $this->assertApiResponse($editedSupplierMasterRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteSupplierMasterRefferedBack()
    {
        $supplierMasterRefferedBack = $this->makeSupplierMasterRefferedBack();
        $this->json('DELETE', '/api/v1/supplierMasterRefferedBacks/'.$supplierMasterRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/supplierMasterRefferedBacks/'.$supplierMasterRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
