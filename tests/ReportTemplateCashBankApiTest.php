<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateCashBankApiTest extends TestCase
{
    use MakeReportTemplateCashBankTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateReportTemplateCashBank()
    {
        $reportTemplateCashBank = $this->fakeReportTemplateCashBankData();
        $this->json('POST', '/api/v1/reportTemplateCashBanks', $reportTemplateCashBank);

        $this->assertApiResponse($reportTemplateCashBank);
    }

    /**
     * @test
     */
    public function testReadReportTemplateCashBank()
    {
        $reportTemplateCashBank = $this->makeReportTemplateCashBank();
        $this->json('GET', '/api/v1/reportTemplateCashBanks/'.$reportTemplateCashBank->id);

        $this->assertApiResponse($reportTemplateCashBank->toArray());
    }

    /**
     * @test
     */
    public function testUpdateReportTemplateCashBank()
    {
        $reportTemplateCashBank = $this->makeReportTemplateCashBank();
        $editedReportTemplateCashBank = $this->fakeReportTemplateCashBankData();

        $this->json('PUT', '/api/v1/reportTemplateCashBanks/'.$reportTemplateCashBank->id, $editedReportTemplateCashBank);

        $this->assertApiResponse($editedReportTemplateCashBank);
    }

    /**
     * @test
     */
    public function testDeleteReportTemplateCashBank()
    {
        $reportTemplateCashBank = $this->makeReportTemplateCashBank();
        $this->json('DELETE', '/api/v1/reportTemplateCashBanks/'.$reportTemplateCashBank->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/reportTemplateCashBanks/'.$reportTemplateCashBank->id);

        $this->assertResponseStatus(404);
    }
}
