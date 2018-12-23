<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankAccountRefferedBackApiTest extends TestCase
{
    use MakeBankAccountRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBankAccountRefferedBack()
    {
        $bankAccountRefferedBack = $this->fakeBankAccountRefferedBackData();
        $this->json('POST', '/api/v1/bankAccountRefferedBacks', $bankAccountRefferedBack);

        $this->assertApiResponse($bankAccountRefferedBack);
    }

    /**
     * @test
     */
    public function testReadBankAccountRefferedBack()
    {
        $bankAccountRefferedBack = $this->makeBankAccountRefferedBack();
        $this->json('GET', '/api/v1/bankAccountRefferedBacks/'.$bankAccountRefferedBack->id);

        $this->assertApiResponse($bankAccountRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBankAccountRefferedBack()
    {
        $bankAccountRefferedBack = $this->makeBankAccountRefferedBack();
        $editedBankAccountRefferedBack = $this->fakeBankAccountRefferedBackData();

        $this->json('PUT', '/api/v1/bankAccountRefferedBacks/'.$bankAccountRefferedBack->id, $editedBankAccountRefferedBack);

        $this->assertApiResponse($editedBankAccountRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteBankAccountRefferedBack()
    {
        $bankAccountRefferedBack = $this->makeBankAccountRefferedBack();
        $this->json('DELETE', '/api/v1/bankAccountRefferedBacks/'.$bankAccountRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/bankAccountRefferedBacks/'.$bankAccountRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
