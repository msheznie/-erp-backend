<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChartOfAccountsRefferedBackApiTest extends TestCase
{
    use MakeChartOfAccountsRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateChartOfAccountsRefferedBack()
    {
        $chartOfAccountsRefferedBack = $this->fakeChartOfAccountsRefferedBackData();
        $this->json('POST', '/api/v1/chartOfAccountsRefferedBacks', $chartOfAccountsRefferedBack);

        $this->assertApiResponse($chartOfAccountsRefferedBack);
    }

    /**
     * @test
     */
    public function testReadChartOfAccountsRefferedBack()
    {
        $chartOfAccountsRefferedBack = $this->makeChartOfAccountsRefferedBack();
        $this->json('GET', '/api/v1/chartOfAccountsRefferedBacks/'.$chartOfAccountsRefferedBack->id);

        $this->assertApiResponse($chartOfAccountsRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateChartOfAccountsRefferedBack()
    {
        $chartOfAccountsRefferedBack = $this->makeChartOfAccountsRefferedBack();
        $editedChartOfAccountsRefferedBack = $this->fakeChartOfAccountsRefferedBackData();

        $this->json('PUT', '/api/v1/chartOfAccountsRefferedBacks/'.$chartOfAccountsRefferedBack->id, $editedChartOfAccountsRefferedBack);

        $this->assertApiResponse($editedChartOfAccountsRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteChartOfAccountsRefferedBack()
    {
        $chartOfAccountsRefferedBack = $this->makeChartOfAccountsRefferedBack();
        $this->json('DELETE', '/api/v1/chartOfAccountsRefferedBacks/'.$chartOfAccountsRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/chartOfAccountsRefferedBacks/'.$chartOfAccountsRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
