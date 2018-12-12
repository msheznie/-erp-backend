<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankReconciliationRefferedBackApiTest extends TestCase
{
    use MakeBankReconciliationRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBankReconciliationRefferedBack()
    {
        $bankReconciliationRefferedBack = $this->fakeBankReconciliationRefferedBackData();
        $this->json('POST', '/api/v1/bankReconciliationRefferedBacks', $bankReconciliationRefferedBack);

        $this->assertApiResponse($bankReconciliationRefferedBack);
    }

    /**
     * @test
     */
    public function testReadBankReconciliationRefferedBack()
    {
        $bankReconciliationRefferedBack = $this->makeBankReconciliationRefferedBack();
        $this->json('GET', '/api/v1/bankReconciliationRefferedBacks/'.$bankReconciliationRefferedBack->id);

        $this->assertApiResponse($bankReconciliationRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBankReconciliationRefferedBack()
    {
        $bankReconciliationRefferedBack = $this->makeBankReconciliationRefferedBack();
        $editedBankReconciliationRefferedBack = $this->fakeBankReconciliationRefferedBackData();

        $this->json('PUT', '/api/v1/bankReconciliationRefferedBacks/'.$bankReconciliationRefferedBack->id, $editedBankReconciliationRefferedBack);

        $this->assertApiResponse($editedBankReconciliationRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteBankReconciliationRefferedBack()
    {
        $bankReconciliationRefferedBack = $this->makeBankReconciliationRefferedBack();
        $this->json('DELETE', '/api/v1/bankReconciliationRefferedBacks/'.$bankReconciliationRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/bankReconciliationRefferedBacks/'.$bankReconciliationRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
