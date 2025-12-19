<?php namespace Tests\Repositories;

use App\Models\CustomReportColumns;
use App\Repositories\CustomReportColumnsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CustomReportColumnsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomReportColumnsRepository
     */
    protected $customReportColumnsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customReportColumnsRepo = \App::make(CustomReportColumnsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_custom_report_columns()
    {
        $customReportColumns = factory(CustomReportColumns::class)->make()->toArray();

        $createdCustomReportColumns = $this->customReportColumnsRepo->create($customReportColumns);

        $createdCustomReportColumns = $createdCustomReportColumns->toArray();
        $this->assertArrayHasKey('id', $createdCustomReportColumns);
        $this->assertNotNull($createdCustomReportColumns['id'], 'Created CustomReportColumns must have id specified');
        $this->assertNotNull(CustomReportColumns::find($createdCustomReportColumns['id']), 'CustomReportColumns with given id must be in DB');
        $this->assertModelData($customReportColumns, $createdCustomReportColumns);
    }

    /**
     * @test read
     */
    public function test_read_custom_report_columns()
    {
        $customReportColumns = factory(CustomReportColumns::class)->create();

        $dbCustomReportColumns = $this->customReportColumnsRepo->find($customReportColumns->id);

        $dbCustomReportColumns = $dbCustomReportColumns->toArray();
        $this->assertModelData($customReportColumns->toArray(), $dbCustomReportColumns);
    }

    /**
     * @test update
     */
    public function test_update_custom_report_columns()
    {
        $customReportColumns = factory(CustomReportColumns::class)->create();
        $fakeCustomReportColumns = factory(CustomReportColumns::class)->make()->toArray();

        $updatedCustomReportColumns = $this->customReportColumnsRepo->update($fakeCustomReportColumns, $customReportColumns->id);

        $this->assertModelData($fakeCustomReportColumns, $updatedCustomReportColumns->toArray());
        $dbCustomReportColumns = $this->customReportColumnsRepo->find($customReportColumns->id);
        $this->assertModelData($fakeCustomReportColumns, $dbCustomReportColumns->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_custom_report_columns()
    {
        $customReportColumns = factory(CustomReportColumns::class)->create();

        $resp = $this->customReportColumnsRepo->delete($customReportColumns->id);

        $this->assertTrue($resp);
        $this->assertNull(CustomReportColumns::find($customReportColumns->id), 'CustomReportColumns should not exist in DB');
    }
}
