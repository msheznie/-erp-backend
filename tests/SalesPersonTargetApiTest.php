<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SalesPersonTargetApiTest extends TestCase
{
    use MakeSalesPersonTargetTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSalesPersonTarget()
    {
        $salesPersonTarget = $this->fakeSalesPersonTargetData();
        $this->json('POST', '/api/v1/salesPersonTargets', $salesPersonTarget);

        $this->assertApiResponse($salesPersonTarget);
    }

    /**
     * @test
     */
    public function testReadSalesPersonTarget()
    {
        $salesPersonTarget = $this->makeSalesPersonTarget();
        $this->json('GET', '/api/v1/salesPersonTargets/'.$salesPersonTarget->id);

        $this->assertApiResponse($salesPersonTarget->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSalesPersonTarget()
    {
        $salesPersonTarget = $this->makeSalesPersonTarget();
        $editedSalesPersonTarget = $this->fakeSalesPersonTargetData();

        $this->json('PUT', '/api/v1/salesPersonTargets/'.$salesPersonTarget->id, $editedSalesPersonTarget);

        $this->assertApiResponse($editedSalesPersonTarget);
    }

    /**
     * @test
     */
    public function testDeleteSalesPersonTarget()
    {
        $salesPersonTarget = $this->makeSalesPersonTarget();
        $this->json('DELETE', '/api/v1/salesPersonTargets/'.$salesPersonTarget->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/salesPersonTargets/'.$salesPersonTarget->id);

        $this->assertResponseStatus(404);
    }
}
