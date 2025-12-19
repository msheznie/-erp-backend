<?php

use App\Models\ReportTemplateCashBank;
use App\Repositories\ReportTemplateCashBankRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateCashBankRepositoryTest extends TestCase
{
    use MakeReportTemplateCashBankTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportTemplateCashBankRepository
     */
    protected $reportTemplateCashBankRepo;

    public function setUp()
    {
        parent::setUp();
        $this->reportTemplateCashBankRepo = App::make(ReportTemplateCashBankRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateReportTemplateCashBank()
    {
        $reportTemplateCashBank = $this->fakeReportTemplateCashBankData();
        $createdReportTemplateCashBank = $this->reportTemplateCashBankRepo->create($reportTemplateCashBank);
        $createdReportTemplateCashBank = $createdReportTemplateCashBank->toArray();
        $this->assertArrayHasKey('id', $createdReportTemplateCashBank);
        $this->assertNotNull($createdReportTemplateCashBank['id'], 'Created ReportTemplateCashBank must have id specified');
        $this->assertNotNull(ReportTemplateCashBank::find($createdReportTemplateCashBank['id']), 'ReportTemplateCashBank with given id must be in DB');
        $this->assertModelData($reportTemplateCashBank, $createdReportTemplateCashBank);
    }

    /**
     * @test read
     */
    public function testReadReportTemplateCashBank()
    {
        $reportTemplateCashBank = $this->makeReportTemplateCashBank();
        $dbReportTemplateCashBank = $this->reportTemplateCashBankRepo->find($reportTemplateCashBank->id);
        $dbReportTemplateCashBank = $dbReportTemplateCashBank->toArray();
        $this->assertModelData($reportTemplateCashBank->toArray(), $dbReportTemplateCashBank);
    }

    /**
     * @test update
     */
    public function testUpdateReportTemplateCashBank()
    {
        $reportTemplateCashBank = $this->makeReportTemplateCashBank();
        $fakeReportTemplateCashBank = $this->fakeReportTemplateCashBankData();
        $updatedReportTemplateCashBank = $this->reportTemplateCashBankRepo->update($fakeReportTemplateCashBank, $reportTemplateCashBank->id);
        $this->assertModelData($fakeReportTemplateCashBank, $updatedReportTemplateCashBank->toArray());
        $dbReportTemplateCashBank = $this->reportTemplateCashBankRepo->find($reportTemplateCashBank->id);
        $this->assertModelData($fakeReportTemplateCashBank, $dbReportTemplateCashBank->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteReportTemplateCashBank()
    {
        $reportTemplateCashBank = $this->makeReportTemplateCashBank();
        $resp = $this->reportTemplateCashBankRepo->delete($reportTemplateCashBank->id);
        $this->assertTrue($resp);
        $this->assertNull(ReportTemplateCashBank::find($reportTemplateCashBank->id), 'ReportTemplateCashBank should not exist in DB');
    }
}
