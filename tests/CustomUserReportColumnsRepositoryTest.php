<?php namespace Tests\Repositories;

use App\Models\CustomUserReportColumns;
use App\Repositories\CustomUserReportColumnsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CustomUserReportColumnsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomUserReportColumnsRepository
     */
    protected $customUserReportColumnsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customUserReportColumnsRepo = \App::make(CustomUserReportColumnsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_custom_user_report_columns()
    {
        $customUserReportColumns = factory(CustomUserReportColumns::class)->make()->toArray();

        $createdCustomUserReportColumns = $this->customUserReportColumnsRepo->create($customUserReportColumns);

        $createdCustomUserReportColumns = $createdCustomUserReportColumns->toArray();
        $this->assertArrayHasKey('id', $createdCustomUserReportColumns);
        $this->assertNotNull($createdCustomUserReportColumns['id'], 'Created CustomUserReportColumns must have id specified');
        $this->assertNotNull(CustomUserReportColumns::find($createdCustomUserReportColumns['id']), 'CustomUserReportColumns with given id must be in DB');
        $this->assertModelData($customUserReportColumns, $createdCustomUserReportColumns);
    }

    /**
     * @test read
     */
    public function test_read_custom_user_report_columns()
    {
        $customUserReportColumns = factory(CustomUserReportColumns::class)->create();

        $dbCustomUserReportColumns = $this->customUserReportColumnsRepo->find($customUserReportColumns->id);

        $dbCustomUserReportColumns = $dbCustomUserReportColumns->toArray();
        $this->assertModelData($customUserReportColumns->toArray(), $dbCustomUserReportColumns);
    }

    /**
     * @test update
     */
    public function test_update_custom_user_report_columns()
    {
        $customUserReportColumns = factory(CustomUserReportColumns::class)->create();
        $fakeCustomUserReportColumns = factory(CustomUserReportColumns::class)->make()->toArray();

        $updatedCustomUserReportColumns = $this->customUserReportColumnsRepo->update($fakeCustomUserReportColumns, $customUserReportColumns->id);

        $this->assertModelData($fakeCustomUserReportColumns, $updatedCustomUserReportColumns->toArray());
        $dbCustomUserReportColumns = $this->customUserReportColumnsRepo->find($customUserReportColumns->id);
        $this->assertModelData($fakeCustomUserReportColumns, $dbCustomUserReportColumns->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_custom_user_report_columns()
    {
        $customUserReportColumns = factory(CustomUserReportColumns::class)->create();

        $resp = $this->customUserReportColumnsRepo->delete($customUserReportColumns->id);

        $this->assertTrue($resp);
        $this->assertNull(CustomUserReportColumns::find($customUserReportColumns->id), 'CustomUserReportColumns should not exist in DB');
    }
}
