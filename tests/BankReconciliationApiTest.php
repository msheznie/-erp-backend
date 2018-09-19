<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankReconciliationApiTest extends TestCase
{
    use MakeBankReconciliationTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBankReconciliation()
    {
        $bankReconciliation = $this->fakeBankReconciliationData();
        $this->json('POST', '/api/v1/bankReconciliations', $bankReconciliation);

        $this->assertApiResponse($bankReconciliation);
    }

    /**
     * @test
     */
    public function testReadBankReconciliation()
    {
        $bankReconciliation = $this->makeBankReconciliation();
        $this->json('GET', '/api/v1/bankReconciliations/'.$bankReconciliation->id);

        $this->assertApiResponse($bankReconciliation->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBankReconciliation()
    {
        $bankReconciliation = $this->makeBankReconciliation();
        $editedBankReconciliation = $this->fakeBankReconciliationData();

        $this->json('PUT', '/api/v1/bankReconciliations/'.$bankReconciliation->id, $editedBankReconciliation);

        $this->assertApiResponse($editedBankReconciliation);
    }

    /**
     * @test
     */
    public function testDeleteBankReconciliation()
    {
        $bankReconciliation = $this->makeBankReconciliation();
        $this->json('DELETE', '/api/v1/bankReconciliations/'.$bankReconciliation->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/bankReconciliations/'.$bankReconciliation->id);

        $this->assertResponseStatus(404);
    }
}
