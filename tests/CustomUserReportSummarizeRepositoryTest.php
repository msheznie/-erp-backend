<?php namespace Tests\Repositories;

use App\Models\CustomUserReportSummarize;
use App\Repositories\CustomUserReportSummarizeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CustomUserReportSummarizeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomUserReportSummarizeRepository
     */
    protected $customUserReportSummarizeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customUserReportSummarizeRepo = \App::make(CustomUserReportSummarizeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_custom_user_report_summarize()
    {
        $customUserReportSummarize = factory(CustomUserReportSummarize::class)->make()->toArray();

        $createdCustomUserReportSummarize = $this->customUserReportSummarizeRepo->create($customUserReportSummarize);

        $createdCustomUserReportSummarize = $createdCustomUserReportSummarize->toArray();
        $this->assertArrayHasKey('id', $createdCustomUserReportSummarize);
        $this->assertNotNull($createdCustomUserReportSummarize['id'], 'Created CustomUserReportSummarize must have id specified');
        $this->assertNotNull(CustomUserReportSummarize::find($createdCustomUserReportSummarize['id']), 'CustomUserReportSummarize with given id must be in DB');
        $this->assertModelData($customUserReportSummarize, $createdCustomUserReportSummarize);
    }

    /**
     * @test read
     */
    public function test_read_custom_user_report_summarize()
    {
        $customUserReportSummarize = factory(CustomUserReportSummarize::class)->create();

        $dbCustomUserReportSummarize = $this->customUserReportSummarizeRepo->find($customUserReportSummarize->id);

        $dbCustomUserReportSummarize = $dbCustomUserReportSummarize->toArray();
        $this->assertModelData($customUserReportSummarize->toArray(), $dbCustomUserReportSummarize);
    }

    /**
     * @test update
     */
    public function test_update_custom_user_report_summarize()
    {
        $customUserReportSummarize = factory(CustomUserReportSummarize::class)->create();
        $fakeCustomUserReportSummarize = factory(CustomUserReportSummarize::class)->make()->toArray();

        $updatedCustomUserReportSummarize = $this->customUserReportSummarizeRepo->update($fakeCustomUserReportSummarize, $customUserReportSummarize->id);

        $this->assertModelData($fakeCustomUserReportSummarize, $updatedCustomUserReportSummarize->toArray());
        $dbCustomUserReportSummarize = $this->customUserReportSummarizeRepo->find($customUserReportSummarize->id);
        $this->assertModelData($fakeCustomUserReportSummarize, $dbCustomUserReportSummarize->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_custom_user_report_summarize()
    {
        $customUserReportSummarize = factory(CustomUserReportSummarize::class)->create();

        $resp = $this->customUserReportSummarizeRepo->delete($customUserReportSummarize->id);

        $this->assertTrue($resp);
        $this->assertNull(CustomUserReportSummarize::find($customUserReportSummarize->id), 'CustomUserReportSummarize should not exist in DB');
    }
}
