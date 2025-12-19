<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SalesPersonMasterApiTest extends TestCase
{
    use MakeSalesPersonMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSalesPersonMaster()
    {
        $salesPersonMaster = $this->fakeSalesPersonMasterData();
        $this->json('POST', '/api/v1/salesPersonMasters', $salesPersonMaster);

        $this->assertApiResponse($salesPersonMaster);
    }

    /**
     * @test
     */
    public function testReadSalesPersonMaster()
    {
        $salesPersonMaster = $this->makeSalesPersonMaster();
        $this->json('GET', '/api/v1/salesPersonMasters/'.$salesPersonMaster->id);

        $this->assertApiResponse($salesPersonMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSalesPersonMaster()
    {
        $salesPersonMaster = $this->makeSalesPersonMaster();
        $editedSalesPersonMaster = $this->fakeSalesPersonMasterData();

        $this->json('PUT', '/api/v1/salesPersonMasters/'.$salesPersonMaster->id, $editedSalesPersonMaster);

        $this->assertApiResponse($editedSalesPersonMaster);
    }

    /**
     * @test
     */
    public function testDeleteSalesPersonMaster()
    {
        $salesPersonMaster = $this->makeSalesPersonMaster();
        $this->json('DELETE', '/api/v1/salesPersonMasters/'.$salesPersonMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/salesPersonMasters/'.$salesPersonMaster->id);

        $this->assertResponseStatus(404);
    }
}
