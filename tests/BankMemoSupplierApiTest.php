<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankMemoSupplierApiTest extends TestCase
{
    use MakeBankMemoSupplierTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBankMemoSupplier()
    {
        $bankMemoSupplier = $this->fakeBankMemoSupplierData();
        $this->json('POST', '/api/v1/bankMemoSuppliers', $bankMemoSupplier);

        $this->assertApiResponse($bankMemoSupplier);
    }

    /**
     * @test
     */
    public function testReadBankMemoSupplier()
    {
        $bankMemoSupplier = $this->makeBankMemoSupplier();
        $this->json('GET', '/api/v1/bankMemoSuppliers/'.$bankMemoSupplier->id);

        $this->assertApiResponse($bankMemoSupplier->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBankMemoSupplier()
    {
        $bankMemoSupplier = $this->makeBankMemoSupplier();
        $editedBankMemoSupplier = $this->fakeBankMemoSupplierData();

        $this->json('PUT', '/api/v1/bankMemoSuppliers/'.$bankMemoSupplier->id, $editedBankMemoSupplier);

        $this->assertApiResponse($editedBankMemoSupplier);
    }

    /**
     * @test
     */
    public function testDeleteBankMemoSupplier()
    {
        $bankMemoSupplier = $this->makeBankMemoSupplier();
        $this->json('DELETE', '/api/v1/bankMemoSuppliers/'.$bankMemoSupplier->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/bankMemoSuppliers/'.$bankMemoSupplier->id);

        $this->assertResponseStatus(404);
    }
}
