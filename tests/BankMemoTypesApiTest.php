<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankMemoTypesApiTest extends TestCase
{
    use MakeBankMemoTypesTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBankMemoTypes()
    {
        $bankMemoTypes = $this->fakeBankMemoTypesData();
        $this->json('POST', '/api/v1/bankMemoTypes', $bankMemoTypes);

        $this->assertApiResponse($bankMemoTypes);
    }

    /**
     * @test
     */
    public function testReadBankMemoTypes()
    {
        $bankMemoTypes = $this->makeBankMemoTypes();
        $this->json('GET', '/api/v1/bankMemoTypes/'.$bankMemoTypes->id);

        $this->assertApiResponse($bankMemoTypes->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBankMemoTypes()
    {
        $bankMemoTypes = $this->makeBankMemoTypes();
        $editedBankMemoTypes = $this->fakeBankMemoTypesData();

        $this->json('PUT', '/api/v1/bankMemoTypes/'.$bankMemoTypes->id, $editedBankMemoTypes);

        $this->assertApiResponse($editedBankMemoTypes);
    }

    /**
     * @test
     */
    public function testDeleteBankMemoTypes()
    {
        $bankMemoTypes = $this->makeBankMemoTypes();
        $this->json('DELETE', '/api/v1/bankMemoTypes/'.$bankMemoTypes->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/bankMemoTypes/'.$bankMemoTypes->id);

        $this->assertResponseStatus(404);
    }
}
