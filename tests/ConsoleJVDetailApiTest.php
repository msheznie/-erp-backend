<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConsoleJVDetailApiTest extends TestCase
{
    use MakeConsoleJVDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateConsoleJVDetail()
    {
        $consoleJVDetail = $this->fakeConsoleJVDetailData();
        $this->json('POST', '/api/v1/consoleJVDetails', $consoleJVDetail);

        $this->assertApiResponse($consoleJVDetail);
    }

    /**
     * @test
     */
    public function testReadConsoleJVDetail()
    {
        $consoleJVDetail = $this->makeConsoleJVDetail();
        $this->json('GET', '/api/v1/consoleJVDetails/'.$consoleJVDetail->id);

        $this->assertApiResponse($consoleJVDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateConsoleJVDetail()
    {
        $consoleJVDetail = $this->makeConsoleJVDetail();
        $editedConsoleJVDetail = $this->fakeConsoleJVDetailData();

        $this->json('PUT', '/api/v1/consoleJVDetails/'.$consoleJVDetail->id, $editedConsoleJVDetail);

        $this->assertApiResponse($editedConsoleJVDetail);
    }

    /**
     * @test
     */
    public function testDeleteConsoleJVDetail()
    {
        $consoleJVDetail = $this->makeConsoleJVDetail();
        $this->json('DELETE', '/api/v1/consoleJVDetails/'.$consoleJVDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/consoleJVDetails/'.$consoleJVDetail->id);

        $this->assertResponseStatus(404);
    }
}
