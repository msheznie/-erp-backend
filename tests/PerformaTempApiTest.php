<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PerformaTempApiTest extends TestCase
{
    use MakePerformaTempTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePerformaTemp()
    {
        $performaTemp = $this->fakePerformaTempData();
        $this->json('POST', '/api/v1/performaTemps', $performaTemp);

        $this->assertApiResponse($performaTemp);
    }

    /**
     * @test
     */
    public function testReadPerformaTemp()
    {
        $performaTemp = $this->makePerformaTemp();
        $this->json('GET', '/api/v1/performaTemps/'.$performaTemp->id);

        $this->assertApiResponse($performaTemp->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePerformaTemp()
    {
        $performaTemp = $this->makePerformaTemp();
        $editedPerformaTemp = $this->fakePerformaTempData();

        $this->json('PUT', '/api/v1/performaTemps/'.$performaTemp->id, $editedPerformaTemp);

        $this->assertApiResponse($editedPerformaTemp);
    }

    /**
     * @test
     */
    public function testDeletePerformaTemp()
    {
        $performaTemp = $this->makePerformaTemp();
        $this->json('DELETE', '/api/v1/performaTemps/'.$performaTemp->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/performaTemps/'.$performaTemp->id);

        $this->assertResponseStatus(404);
    }
}
