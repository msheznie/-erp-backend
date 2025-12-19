<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MonthlyAdditionsMasterApiTest extends TestCase
{
    use MakeMonthlyAdditionsMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateMonthlyAdditionsMaster()
    {
        $monthlyAdditionsMaster = $this->fakeMonthlyAdditionsMasterData();
        $this->json('POST', '/api/v1/monthlyAdditionsMasters', $monthlyAdditionsMaster);

        $this->assertApiResponse($monthlyAdditionsMaster);
    }

    /**
     * @test
     */
    public function testReadMonthlyAdditionsMaster()
    {
        $monthlyAdditionsMaster = $this->makeMonthlyAdditionsMaster();
        $this->json('GET', '/api/v1/monthlyAdditionsMasters/'.$monthlyAdditionsMaster->id);

        $this->assertApiResponse($monthlyAdditionsMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateMonthlyAdditionsMaster()
    {
        $monthlyAdditionsMaster = $this->makeMonthlyAdditionsMaster();
        $editedMonthlyAdditionsMaster = $this->fakeMonthlyAdditionsMasterData();

        $this->json('PUT', '/api/v1/monthlyAdditionsMasters/'.$monthlyAdditionsMaster->id, $editedMonthlyAdditionsMaster);

        $this->assertApiResponse($editedMonthlyAdditionsMaster);
    }

    /**
     * @test
     */
    public function testDeleteMonthlyAdditionsMaster()
    {
        $monthlyAdditionsMaster = $this->makeMonthlyAdditionsMaster();
        $this->json('DELETE', '/api/v1/monthlyAdditionsMasters/'.$monthlyAdditionsMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/monthlyAdditionsMasters/'.$monthlyAdditionsMaster->id);

        $this->assertResponseStatus(404);
    }
}
