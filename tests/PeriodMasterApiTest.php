<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PeriodMasterApiTest extends TestCase
{
    use MakePeriodMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePeriodMaster()
    {
        $periodMaster = $this->fakePeriodMasterData();
        $this->json('POST', '/api/v1/periodMasters', $periodMaster);

        $this->assertApiResponse($periodMaster);
    }

    /**
     * @test
     */
    public function testReadPeriodMaster()
    {
        $periodMaster = $this->makePeriodMaster();
        $this->json('GET', '/api/v1/periodMasters/'.$periodMaster->id);

        $this->assertApiResponse($periodMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePeriodMaster()
    {
        $periodMaster = $this->makePeriodMaster();
        $editedPeriodMaster = $this->fakePeriodMasterData();

        $this->json('PUT', '/api/v1/periodMasters/'.$periodMaster->id, $editedPeriodMaster);

        $this->assertApiResponse($editedPeriodMaster);
    }

    /**
     * @test
     */
    public function testDeletePeriodMaster()
    {
        $periodMaster = $this->makePeriodMaster();
        $this->json('DELETE', '/api/v1/periodMasters/'.$periodMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/periodMasters/'.$periodMaster->id);

        $this->assertResponseStatus(404);
    }
}
