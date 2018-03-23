<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankMemoSupplierMasterApiTest extends TestCase
{
    use MakeBankMemoSupplierMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBankMemoSupplierMaster()
    {
        $bankMemoSupplierMaster = $this->fakeBankMemoSupplierMasterData();
        $this->json('POST', '/api/v1/bankMemoSupplierMasters', $bankMemoSupplierMaster);

        $this->assertApiResponse($bankMemoSupplierMaster);
    }

    /**
     * @test
     */
    public function testReadBankMemoSupplierMaster()
    {
        $bankMemoSupplierMaster = $this->makeBankMemoSupplierMaster();
        $this->json('GET', '/api/v1/bankMemoSupplierMasters/'.$bankMemoSupplierMaster->id);

        $this->assertApiResponse($bankMemoSupplierMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBankMemoSupplierMaster()
    {
        $bankMemoSupplierMaster = $this->makeBankMemoSupplierMaster();
        $editedBankMemoSupplierMaster = $this->fakeBankMemoSupplierMasterData();

        $this->json('PUT', '/api/v1/bankMemoSupplierMasters/'.$bankMemoSupplierMaster->id, $editedBankMemoSupplierMaster);

        $this->assertApiResponse($editedBankMemoSupplierMaster);
    }

    /**
     * @test
     */
    public function testDeleteBankMemoSupplierMaster()
    {
        $bankMemoSupplierMaster = $this->makeBankMemoSupplierMaster();
        $this->json('DELETE', '/api/v1/bankMemoSupplierMasters/'.$bankMemoSupplierMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/bankMemoSupplierMasters/'.$bankMemoSupplierMaster->id);

        $this->assertResponseStatus(404);
    }
}
