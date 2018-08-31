<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnbilledGRVApiTest extends TestCase
{
    use MakeUnbilledGRVTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateUnbilledGRV()
    {
        $unbilledGRV = $this->fakeUnbilledGRVData();
        $this->json('POST', '/api/v1/unbilledGRVs', $unbilledGRV);

        $this->assertApiResponse($unbilledGRV);
    }

    /**
     * @test
     */
    public function testReadUnbilledGRV()
    {
        $unbilledGRV = $this->makeUnbilledGRV();
        $this->json('GET', '/api/v1/unbilledGRVs/'.$unbilledGRV->id);

        $this->assertApiResponse($unbilledGRV->toArray());
    }

    /**
     * @test
     */
    public function testUpdateUnbilledGRV()
    {
        $unbilledGRV = $this->makeUnbilledGRV();
        $editedUnbilledGRV = $this->fakeUnbilledGRVData();

        $this->json('PUT', '/api/v1/unbilledGRVs/'.$unbilledGRV->id, $editedUnbilledGRV);

        $this->assertApiResponse($editedUnbilledGRV);
    }

    /**
     * @test
     */
    public function testDeleteUnbilledGRV()
    {
        $unbilledGRV = $this->makeUnbilledGRV();
        $this->json('DELETE', '/api/v1/unbilledGRVs/'.$unbilledGRV->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/unbilledGRVs/'.$unbilledGRV->id);

        $this->assertResponseStatus(404);
    }
}
