<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChartOfAccountsAssignedApiTest extends TestCase
{
    use MakeChartOfAccountsAssignedTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateChartOfAccountsAssigned()
    {
        $chartOfAccountsAssigned = $this->fakeChartOfAccountsAssignedData();
        $this->json('POST', '/api/v1/chartOfAccountsAssigneds', $chartOfAccountsAssigned);

        $this->assertApiResponse($chartOfAccountsAssigned);
    }

    /**
     * @test
     */
    public function testReadChartOfAccountsAssigned()
    {
        $chartOfAccountsAssigned = $this->makeChartOfAccountsAssigned();
        $this->json('GET', '/api/v1/chartOfAccountsAssigneds/'.$chartOfAccountsAssigned->id);

        $this->assertApiResponse($chartOfAccountsAssigned->toArray());
    }

    /**
     * @test
     */
    public function testUpdateChartOfAccountsAssigned()
    {
        $chartOfAccountsAssigned = $this->makeChartOfAccountsAssigned();
        $editedChartOfAccountsAssigned = $this->fakeChartOfAccountsAssignedData();

        $this->json('PUT', '/api/v1/chartOfAccountsAssigneds/'.$chartOfAccountsAssigned->id, $editedChartOfAccountsAssigned);

        $this->assertApiResponse($editedChartOfAccountsAssigned);
    }

    /**
     * @test
     */
    public function testDeleteChartOfAccountsAssigned()
    {
        $chartOfAccountsAssigned = $this->makeChartOfAccountsAssigned();
        $this->json('DELETE', '/api/v1/chartOfAccountsAssigneds/'.$chartOfAccountsAssigned->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/chartOfAccountsAssigneds/'.$chartOfAccountsAssigned->id);

        $this->assertResponseStatus(404);
    }
}
