<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankMemoPayeeApiTest extends TestCase
{
    use MakeBankMemoPayeeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBankMemoPayee()
    {
        $bankMemoPayee = $this->fakeBankMemoPayeeData();
        $this->json('POST', '/api/v1/bankMemoPayees', $bankMemoPayee);

        $this->assertApiResponse($bankMemoPayee);
    }

    /**
     * @test
     */
    public function testReadBankMemoPayee()
    {
        $bankMemoPayee = $this->makeBankMemoPayee();
        $this->json('GET', '/api/v1/bankMemoPayees/'.$bankMemoPayee->id);

        $this->assertApiResponse($bankMemoPayee->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBankMemoPayee()
    {
        $bankMemoPayee = $this->makeBankMemoPayee();
        $editedBankMemoPayee = $this->fakeBankMemoPayeeData();

        $this->json('PUT', '/api/v1/bankMemoPayees/'.$bankMemoPayee->id, $editedBankMemoPayee);

        $this->assertApiResponse($editedBankMemoPayee);
    }

    /**
     * @test
     */
    public function testDeleteBankMemoPayee()
    {
        $bankMemoPayee = $this->makeBankMemoPayee();
        $this->json('DELETE', '/api/v1/bankMemoPayees/'.$bankMemoPayee->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/bankMemoPayees/'.$bankMemoPayee->id);

        $this->assertResponseStatus(404);
    }
}
