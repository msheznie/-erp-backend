<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PerformaDetailsApiTest extends TestCase
{
    use MakePerformaDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePerformaDetails()
    {
        $performaDetails = $this->fakePerformaDetailsData();
        $this->json('POST', '/api/v1/performaDetails', $performaDetails);

        $this->assertApiResponse($performaDetails);
    }

    /**
     * @test
     */
    public function testReadPerformaDetails()
    {
        $performaDetails = $this->makePerformaDetails();
        $this->json('GET', '/api/v1/performaDetails/'.$performaDetails->id);

        $this->assertApiResponse($performaDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePerformaDetails()
    {
        $performaDetails = $this->makePerformaDetails();
        $editedPerformaDetails = $this->fakePerformaDetailsData();

        $this->json('PUT', '/api/v1/performaDetails/'.$performaDetails->id, $editedPerformaDetails);

        $this->assertApiResponse($editedPerformaDetails);
    }

    /**
     * @test
     */
    public function testDeletePerformaDetails()
    {
        $performaDetails = $this->makePerformaDetails();
        $this->json('DELETE', '/api/v1/performaDetails/'.$performaDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/performaDetails/'.$performaDetails->id);

        $this->assertResponseStatus(404);
    }
}
