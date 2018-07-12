<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class YearApiTest extends TestCase
{
    use MakeYearTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateYear()
    {
        $year = $this->fakeYearData();
        $this->json('POST', '/api/v1/years', $year);

        $this->assertApiResponse($year);
    }

    /**
     * @test
     */
    public function testReadYear()
    {
        $year = $this->makeYear();
        $this->json('GET', '/api/v1/years/'.$year->id);

        $this->assertApiResponse($year->toArray());
    }

    /**
     * @test
     */
    public function testUpdateYear()
    {
        $year = $this->makeYear();
        $editedYear = $this->fakeYearData();

        $this->json('PUT', '/api/v1/years/'.$year->id, $editedYear);

        $this->assertApiResponse($editedYear);
    }

    /**
     * @test
     */
    public function testDeleteYear()
    {
        $year = $this->makeYear();
        $this->json('DELETE', '/api/v1/years/'.$year->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/years/'.$year->id);

        $this->assertResponseStatus(404);
    }
}
