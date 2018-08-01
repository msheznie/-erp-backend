<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContractApiTest extends TestCase
{
    use MakeContractTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateContract()
    {
        $contract = $this->fakeContractData();
        $this->json('POST', '/api/v1/contracts', $contract);

        $this->assertApiResponse($contract);
    }

    /**
     * @test
     */
    public function testReadContract()
    {
        $contract = $this->makeContract();
        $this->json('GET', '/api/v1/contracts/'.$contract->id);

        $this->assertApiResponse($contract->toArray());
    }

    /**
     * @test
     */
    public function testUpdateContract()
    {
        $contract = $this->makeContract();
        $editedContract = $this->fakeContractData();

        $this->json('PUT', '/api/v1/contracts/'.$contract->id, $editedContract);

        $this->assertApiResponse($editedContract);
    }

    /**
     * @test
     */
    public function testDeleteContract()
    {
        $contract = $this->makeContract();
        $this->json('DELETE', '/api/v1/contracts/'.$contract->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/contracts/'.$contract->id);

        $this->assertResponseStatus(404);
    }
}
