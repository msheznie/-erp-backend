<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankAssignApiTest extends TestCase
{
    use MakeBankAssignTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBankAssign()
    {
        $bankAssign = $this->fakeBankAssignData();
        $this->json('POST', '/api/v1/bankAssigns', $bankAssign);

        $this->assertApiResponse($bankAssign);
    }

    /**
     * @test
     */
    public function testReadBankAssign()
    {
        $bankAssign = $this->makeBankAssign();
        $this->json('GET', '/api/v1/bankAssigns/'.$bankAssign->id);

        $this->assertApiResponse($bankAssign->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBankAssign()
    {
        $bankAssign = $this->makeBankAssign();
        $editedBankAssign = $this->fakeBankAssignData();

        $this->json('PUT', '/api/v1/bankAssigns/'.$bankAssign->id, $editedBankAssign);

        $this->assertApiResponse($editedBankAssign);
    }

    /**
     * @test
     */
    public function testDeleteBankAssign()
    {
        $bankAssign = $this->makeBankAssign();
        $this->json('DELETE', '/api/v1/bankAssigns/'.$bankAssign->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/bankAssigns/'.$bankAssign->id);

        $this->assertResponseStatus(404);
    }
}
