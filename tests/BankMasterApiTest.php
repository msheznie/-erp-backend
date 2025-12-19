<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankMasterApiTest extends TestCase
{
    use MakeBankMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBankMaster()
    {
        $bankMaster = $this->fakeBankMasterData();
        $this->json('POST', '/api/v1/bankMasters', $bankMaster);

        $this->assertApiResponse($bankMaster);
    }

    /**
     * @test
     */
    public function testReadBankMaster()
    {
        $bankMaster = $this->makeBankMaster();
        $this->json('GET', '/api/v1/bankMasters/'.$bankMaster->id);

        $this->assertApiResponse($bankMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBankMaster()
    {
        $bankMaster = $this->makeBankMaster();
        $editedBankMaster = $this->fakeBankMasterData();

        $this->json('PUT', '/api/v1/bankMasters/'.$bankMaster->id, $editedBankMaster);

        $this->assertApiResponse($editedBankMaster);
    }

    /**
     * @test
     */
    public function testDeleteBankMaster()
    {
        $bankMaster = $this->makeBankMaster();
        $this->json('DELETE', '/api/v1/bankMasters/'.$bankMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/bankMasters/'.$bankMaster->id);

        $this->assertResponseStatus(404);
    }
}
