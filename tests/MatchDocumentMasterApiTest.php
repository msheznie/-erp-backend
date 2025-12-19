<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MatchDocumentMasterApiTest extends TestCase
{
    use MakeMatchDocumentMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateMatchDocumentMaster()
    {
        $matchDocumentMaster = $this->fakeMatchDocumentMasterData();
        $this->json('POST', '/api/v1/matchDocumentMasters', $matchDocumentMaster);

        $this->assertApiResponse($matchDocumentMaster);
    }

    /**
     * @test
     */
    public function testReadMatchDocumentMaster()
    {
        $matchDocumentMaster = $this->makeMatchDocumentMaster();
        $this->json('GET', '/api/v1/matchDocumentMasters/'.$matchDocumentMaster->id);

        $this->assertApiResponse($matchDocumentMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateMatchDocumentMaster()
    {
        $matchDocumentMaster = $this->makeMatchDocumentMaster();
        $editedMatchDocumentMaster = $this->fakeMatchDocumentMasterData();

        $this->json('PUT', '/api/v1/matchDocumentMasters/'.$matchDocumentMaster->id, $editedMatchDocumentMaster);

        $this->assertApiResponse($editedMatchDocumentMaster);
    }

    /**
     * @test
     */
    public function testDeleteMatchDocumentMaster()
    {
        $matchDocumentMaster = $this->makeMatchDocumentMaster();
        $this->json('DELETE', '/api/v1/matchDocumentMasters/'.$matchDocumentMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/matchDocumentMasters/'.$matchDocumentMaster->id);

        $this->assertResponseStatus(404);
    }
}
