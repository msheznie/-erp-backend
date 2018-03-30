<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankAccountApiTest extends TestCase
{
    use MakeBankAccountTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBankAccount()
    {
        $bankAccount = $this->fakeBankAccountData();
        $this->json('POST', '/api/v1/bankAccounts', $bankAccount);

        $this->assertApiResponse($bankAccount);
    }

    /**
     * @test
     */
    public function testReadBankAccount()
    {
        $bankAccount = $this->makeBankAccount();
        $this->json('GET', '/api/v1/bankAccounts/'.$bankAccount->id);

        $this->assertApiResponse($bankAccount->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBankAccount()
    {
        $bankAccount = $this->makeBankAccount();
        $editedBankAccount = $this->fakeBankAccountData();

        $this->json('PUT', '/api/v1/bankAccounts/'.$bankAccount->id, $editedBankAccount);

        $this->assertApiResponse($editedBankAccount);
    }

    /**
     * @test
     */
    public function testDeleteBankAccount()
    {
        $bankAccount = $this->makeBankAccount();
        $this->json('DELETE', '/api/v1/bankAccounts/'.$bankAccount->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/bankAccounts/'.$bankAccount->id);

        $this->assertResponseStatus(404);
    }
}
