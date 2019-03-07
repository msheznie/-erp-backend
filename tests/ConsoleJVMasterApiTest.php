<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConsoleJVMasterApiTest extends TestCase
{
    use MakeConsoleJVMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateConsoleJVMaster()
    {
        $consoleJVMaster = $this->fakeConsoleJVMasterData();
        $this->json('POST', '/api/v1/consoleJVMasters', $consoleJVMaster);

        $this->assertApiResponse($consoleJVMaster);
    }

    /**
     * @test
     */
    public function testReadConsoleJVMaster()
    {
        $consoleJVMaster = $this->makeConsoleJVMaster();
        $this->json('GET', '/api/v1/consoleJVMasters/'.$consoleJVMaster->id);

        $this->assertApiResponse($consoleJVMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateConsoleJVMaster()
    {
        $consoleJVMaster = $this->makeConsoleJVMaster();
        $editedConsoleJVMaster = $this->fakeConsoleJVMasterData();

        $this->json('PUT', '/api/v1/consoleJVMasters/'.$consoleJVMaster->id, $editedConsoleJVMaster);

        $this->assertApiResponse($editedConsoleJVMaster);
    }

    /**
     * @test
     */
    public function testDeleteConsoleJVMaster()
    {
        $consoleJVMaster = $this->makeConsoleJVMaster();
        $this->json('DELETE', '/api/v1/consoleJVMasters/'.$consoleJVMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/consoleJVMasters/'.$consoleJVMaster->id);

        $this->assertResponseStatus(404);
    }
}
