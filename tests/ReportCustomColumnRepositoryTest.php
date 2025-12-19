<?php namespace Tests\Repositories;

use App\Models\ReportCustomColumn;
use App\Repositories\ReportCustomColumnRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ReportCustomColumnRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportCustomColumnRepository
     */
    protected $reportCustomColumnRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->reportCustomColumnRepo = \App::make(ReportCustomColumnRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_report_custom_column()
    {
        $reportCustomColumn = factory(ReportCustomColumn::class)->make()->toArray();

        $createdReportCustomColumn = $this->reportCustomColumnRepo->create($reportCustomColumn);

        $createdReportCustomColumn = $createdReportCustomColumn->toArray();
        $this->assertArrayHasKey('id', $createdReportCustomColumn);
        $this->assertNotNull($createdReportCustomColumn['id'], 'Created ReportCustomColumn must have id specified');
        $this->assertNotNull(ReportCustomColumn::find($createdReportCustomColumn['id']), 'ReportCustomColumn with given id must be in DB');
        $this->assertModelData($reportCustomColumn, $createdReportCustomColumn);
    }

    /**
     * @test read
     */
    public function test_read_report_custom_column()
    {
        $reportCustomColumn = factory(ReportCustomColumn::class)->create();

        $dbReportCustomColumn = $this->reportCustomColumnRepo->find($reportCustomColumn->id);

        $dbReportCustomColumn = $dbReportCustomColumn->toArray();
        $this->assertModelData($reportCustomColumn->toArray(), $dbReportCustomColumn);
    }

    /**
     * @test update
     */
    public function test_update_report_custom_column()
    {
        $reportCustomColumn = factory(ReportCustomColumn::class)->create();
        $fakeReportCustomColumn = factory(ReportCustomColumn::class)->make()->toArray();

        $updatedReportCustomColumn = $this->reportCustomColumnRepo->update($fakeReportCustomColumn, $reportCustomColumn->id);

        $this->assertModelData($fakeReportCustomColumn, $updatedReportCustomColumn->toArray());
        $dbReportCustomColumn = $this->reportCustomColumnRepo->find($reportCustomColumn->id);
        $this->assertModelData($fakeReportCustomColumn, $dbReportCustomColumn->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_report_custom_column()
    {
        $reportCustomColumn = factory(ReportCustomColumn::class)->create();

        $resp = $this->reportCustomColumnRepo->delete($reportCustomColumn->id);

        $this->assertTrue($resp);
        $this->assertNull(ReportCustomColumn::find($reportCustomColumn->id), 'ReportCustomColumn should not exist in DB');
    }
}
