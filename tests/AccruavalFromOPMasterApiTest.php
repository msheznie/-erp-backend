<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccruavalFromOPMasterApiTest extends TestCase
{
    use MakeAccruavalFromOPMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAccruavalFromOPMaster()
    {
        $accruavalFromOPMaster = $this->fakeAccruavalFromOPMasterData();
        $this->json('POST', '/api/v1/accruavalFromOPMasters', $accruavalFromOPMaster);

        $this->assertApiResponse($accruavalFromOPMaster);
    }

    /**
     * @test
     */
    public function testReadAccruavalFromOPMaster()
    {
        $accruavalFromOPMaster = $this->makeAccruavalFromOPMaster();
        $this->json('GET', '/api/v1/accruavalFromOPMasters/'.$accruavalFromOPMaster->id);

        $this->assertApiResponse($accruavalFromOPMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAccruavalFromOPMaster()
    {
        $accruavalFromOPMaster = $this->makeAccruavalFromOPMaster();
        $editedAccruavalFromOPMaster = $this->fakeAccruavalFromOPMasterData();

        $this->json('PUT', '/api/v1/accruavalFromOPMasters/'.$accruavalFromOPMaster->id, $editedAccruavalFromOPMaster);

        $this->assertApiResponse($editedAccruavalFromOPMaster);
    }

    /**
     * @test
     */
    public function testDeleteAccruavalFromOPMaster()
    {
        $accruavalFromOPMaster = $this->makeAccruavalFromOPMaster();
        $this->json('DELETE', '/api/v1/accruavalFromOPMasters/'.$accruavalFromOPMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/accruavalFromOPMasters/'.$accruavalFromOPMaster->id);

        $this->assertResponseStatus(404);
    }
}
