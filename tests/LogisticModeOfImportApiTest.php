<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogisticModeOfImportApiTest extends TestCase
{
    use MakeLogisticModeOfImportTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateLogisticModeOfImport()
    {
        $logisticModeOfImport = $this->fakeLogisticModeOfImportData();
        $this->json('POST', '/api/v1/logisticModeOfImports', $logisticModeOfImport);

        $this->assertApiResponse($logisticModeOfImport);
    }

    /**
     * @test
     */
    public function testReadLogisticModeOfImport()
    {
        $logisticModeOfImport = $this->makeLogisticModeOfImport();
        $this->json('GET', '/api/v1/logisticModeOfImports/'.$logisticModeOfImport->id);

        $this->assertApiResponse($logisticModeOfImport->toArray());
    }

    /**
     * @test
     */
    public function testUpdateLogisticModeOfImport()
    {
        $logisticModeOfImport = $this->makeLogisticModeOfImport();
        $editedLogisticModeOfImport = $this->fakeLogisticModeOfImportData();

        $this->json('PUT', '/api/v1/logisticModeOfImports/'.$logisticModeOfImport->id, $editedLogisticModeOfImport);

        $this->assertApiResponse($editedLogisticModeOfImport);
    }

    /**
     * @test
     */
    public function testDeleteLogisticModeOfImport()
    {
        $logisticModeOfImport = $this->makeLogisticModeOfImport();
        $this->json('DELETE', '/api/v1/logisticModeOfImports/'.$logisticModeOfImport->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/logisticModeOfImports/'.$logisticModeOfImport->id);

        $this->assertResponseStatus(404);
    }
}
