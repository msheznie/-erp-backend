<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuditTrailApiTest extends TestCase
{
    use MakeAuditTrailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAuditTrail()
    {
        $auditTrail = $this->fakeAuditTrailData();
        $this->json('POST', '/api/v1/auditTrails', $auditTrail);

        $this->assertApiResponse($auditTrail);
    }

    /**
     * @test
     */
    public function testReadAuditTrail()
    {
        $auditTrail = $this->makeAuditTrail();
        $this->json('GET', '/api/v1/auditTrails/'.$auditTrail->id);

        $this->assertApiResponse($auditTrail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAuditTrail()
    {
        $auditTrail = $this->makeAuditTrail();
        $editedAuditTrail = $this->fakeAuditTrailData();

        $this->json('PUT', '/api/v1/auditTrails/'.$auditTrail->id, $editedAuditTrail);

        $this->assertApiResponse($editedAuditTrail);
    }

    /**
     * @test
     */
    public function testDeleteAuditTrail()
    {
        $auditTrail = $this->makeAuditTrail();
        $this->json('DELETE', '/api/v1/auditTrails/'.$auditTrail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/auditTrails/'.$auditTrail->id);

        $this->assertResponseStatus(404);
    }
}
