<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HRMSChartOfAccountsApiTest extends TestCase
{
    use MakeHRMSChartOfAccountsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateHRMSChartOfAccounts()
    {
        $hRMSChartOfAccounts = $this->fakeHRMSChartOfAccountsData();
        $this->json('POST', '/api/v1/hRMSChartOfAccounts', $hRMSChartOfAccounts);

        $this->assertApiResponse($hRMSChartOfAccounts);
    }

    /**
     * @test
     */
    public function testReadHRMSChartOfAccounts()
    {
        $hRMSChartOfAccounts = $this->makeHRMSChartOfAccounts();
        $this->json('GET', '/api/v1/hRMSChartOfAccounts/'.$hRMSChartOfAccounts->id);

        $this->assertApiResponse($hRMSChartOfAccounts->toArray());
    }

    /**
     * @test
     */
    public function testUpdateHRMSChartOfAccounts()
    {
        $hRMSChartOfAccounts = $this->makeHRMSChartOfAccounts();
        $editedHRMSChartOfAccounts = $this->fakeHRMSChartOfAccountsData();

        $this->json('PUT', '/api/v1/hRMSChartOfAccounts/'.$hRMSChartOfAccounts->id, $editedHRMSChartOfAccounts);

        $this->assertApiResponse($editedHRMSChartOfAccounts);
    }

    /**
     * @test
     */
    public function testDeleteHRMSChartOfAccounts()
    {
        $hRMSChartOfAccounts = $this->makeHRMSChartOfAccounts();
        $this->json('DELETE', '/api/v1/hRMSChartOfAccounts/'.$hRMSChartOfAccounts->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/hRMSChartOfAccounts/'.$hRMSChartOfAccounts->id);

        $this->assertResponseStatus(404);
    }
}
