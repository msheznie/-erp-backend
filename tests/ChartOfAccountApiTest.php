<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChartOfAccountApiTest extends TestCase
{
    use MakeChartOfAccountTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateChartOfAccount()
    {
        $chartOfAccount = $this->fakeChartOfAccountData();
        $this->json('POST', '/api/v1/chartOfAccounts', $chartOfAccount);

        $this->assertApiResponse($chartOfAccount);
    }

    /**
     * @test
     */
    public function testReadChartOfAccount()
    {
        $chartOfAccount = $this->makeChartOfAccount();
        $this->json('GET', '/api/v1/chartOfAccounts/'.$chartOfAccount->id);

        $this->assertApiResponse($chartOfAccount->toArray());
    }

    /**
     * @test
     */
    public function testUpdateChartOfAccount()
    {
        $chartOfAccount = $this->makeChartOfAccount();
        $editedChartOfAccount = $this->fakeChartOfAccountData();

        $this->json('PUT', '/api/v1/chartOfAccounts/'.$chartOfAccount->id, $editedChartOfAccount);

        $this->assertApiResponse($editedChartOfAccount);
    }

    /**
     * @test
     */
    public function testDeleteChartOfAccount()
    {
        $chartOfAccount = $this->makeChartOfAccount();
        $this->json('DELETE', '/api/v1/chartOfAccounts/'.$chartOfAccount->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/chartOfAccounts/'.$chartOfAccount->id);

        $this->assertResponseStatus(404);
    }
}
