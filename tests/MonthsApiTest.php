<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MonthsApiTest extends TestCase
{
    use MakeMonthsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateMonths()
    {
        $months = $this->fakeMonthsData();
        $this->json('POST', '/api/v1/months', $months);

        $this->assertApiResponse($months);
    }

    /**
     * @test
     */
    public function testReadMonths()
    {
        $months = $this->makeMonths();
        $this->json('GET', '/api/v1/months/'.$months->id);

        $this->assertApiResponse($months->toArray());
    }

    /**
     * @test
     */
    public function testUpdateMonths()
    {
        $months = $this->makeMonths();
        $editedMonths = $this->fakeMonthsData();

        $this->json('PUT', '/api/v1/months/'.$months->id, $editedMonths);

        $this->assertApiResponse($editedMonths);
    }

    /**
     * @test
     */
    public function testDeleteMonths()
    {
        $months = $this->makeMonths();
        $this->json('DELETE', '/api/v1/months/'.$months->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/months/'.$months->id);

        $this->assertResponseStatus(404);
    }
}
